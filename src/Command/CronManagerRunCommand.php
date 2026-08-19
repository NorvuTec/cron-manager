<?php

namespace Norvutec\CronManagerBundle\Command;

use Norvutec\CronManagerBundle\Entity\CronJobHistory;
use Norvutec\CronManagerBundle\Model\CronJobStatus;
use Norvutec\CronManagerBundle\Model\Exception\CronjobNotFoundException;
use Norvutec\CronManagerBundle\Model\Exception\UnableToForceLockJobException;
use Norvutec\CronManagerBundle\Repository\CronJobHistoryRepository;
use Norvutec\CronManagerBundle\Service\CronManagerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: "cron-manager:run",
    description: 'Runs all currently scheduled cronjobs'
)]
class CronManagerRunCommand extends Command
{
    public function __construct(
        private readonly string                 $projectDir,
        private readonly CronManagerService     $service,
        private readonly CronJobHistoryRepository $historyRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('job', InputArgument::OPTIONAL, 'Run only the single job by job tag.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force run the job.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $job = null;
        try {
            $forceJob = (bool) $input->getOption('force');
            if ($input->getArgument('job')) {
                $job = $this->service->findCronjobForExecution(
                    $input->getArgument('job'), $forceJob
                );
            } else {
                $job = $this->service->findNextCronjobForExecution();
            }
            if ($job === null) {
                $output->writeln("<info>[OK] No cronjob to run</info>");
                return Command::SUCCESS;
            }

            $finder = new PhpExecutableFinder();
            $phpExecutable = $finder->find();
            if ($phpExecutable === false) {
                $output->writeln('<error>[ERROR] Could not locate PHP executable.</error>');
                return Command::FAILURE;
            }

            // Build process as argument array to avoid shell escaping issues across Symfony versions.
            $command = [
                $phpExecutable,
                '--define',
                'max_execution_time=' . ini_get('max_execution_time'),
                '--define',
                'memory_limit=' . ini_get('memory_limit'),
                $this->projectDir . '/bin/console',
                $job->getCommand(),
                ...$job->getExecutionArgs(),
            ];
            $process = new Process($command, timeout: 60);
            $jobHistory = new CronJobHistory();
            $jobHistory->setTag($job->getTag());
            $jobHistory->setName($job->getName());
            $jobHistory->setRunAt(new \DateTime());
            $jobHistory->setHost(gethostname());
            $jobHistory->setStatus(CronJobStatus::RUNNING);
            $this->historyRepository->save($jobHistory, true);
            $process->start(function (string $type, string $buffer) use ($jobHistory, $output): void {
                if (Process::ERR === $type) {
                    $jobHistory->addError($buffer);
                    $output->writeln('<error>'.$buffer.'</error>');
                } else {
                    $jobHistory->addOutput($buffer);
                    $output->writeln('<info>'.$buffer.'</info>');
                }
            });
            while ($process->isRunning()) {
                time_nanosleep(0, 100000000);
            }
            $jobHistory->setExitCode($process->getExitCode());
            $jobHistory->setExitAt(new \DateTime());
            $this->historyRepository->save($jobHistory, true);
        } catch (CronjobNotFoundException $e) {
            $output->writeln("<error>[ERROR] {$e->getMessage()}</error>");
            return Command::INVALID;
        } catch (UnableToForceLockJobException $e) {
            $output->writeln("<error>[ERROR] {$e->getMessage()}</error>");
            return Command::FAILURE;
        } finally {
            if ($job !== null) {
                $this->service->releaseRunLock($job);
            }
        }

        return Command::SUCCESS;
    }


}