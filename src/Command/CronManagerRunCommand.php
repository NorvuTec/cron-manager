<?php

namespace Norvutec\CronManagerBundle\Command;

use Cron\Cron;
use Norvutec\CronManagerBundle\Model\Exception\CronjobNotFoundException;
use Norvutec\CronManagerBundle\Model\Exception\CronManagerException;
use Norvutec\CronManagerBundle\Model\Exception\UnableToForceLockJobException;
use Norvutec\CronManagerBundle\Service\CronManagerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\PhpExecutableFinder;

#[AsCommand(
    name: "cron-manager:run",
    description: 'Runs all currently scheduled cronjobs'
)]
class CronManagerRunCommand extends Command
{
    public function __construct(
        private readonly ContainerInterface     $container,
        private readonly CronManagerService     $service
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('job', InputArgument::OPTIONAL, 'Run only the single job by job tag.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force run the job.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $job = null;
        try {
            $cron = new Cron();
            $cron->setExecutor($this->container->get('cronmanager.executor'));
            $forceJob = $input->getParameterOption('--force') !== false;
            if ($input->getArgument('job')) {
                $job = $this->service->findCronjobForExecution(
                    $input->getArgument('job'), $forceJob
                );
            } else {
                $job = $this->service->findNextCronjobForExecution();
            }
            if($job == null) {
                $output->writeln("<info>[OK] No cronjob to run</info>");
                return Command::SUCCESS;
            }

            $finder = new PhpExecutableFinder();
            $phpExecutable = $finder->find();
            $rootDir = $this->container->getParameter('kernel.project_dir');

            // TODO Exeuction of the job

        } catch (CronjobNotFoundException $e) {
            $output->writeln("<error>[ERROR] {$e->getMessage()}</error>");
            return Command::INVALID;
        } catch (UnableToForceLockJobException $e) {
            $output->writeln("<error>[ERROR] {$e->getMessage()}</error>");
            return Command::FAILURE;
        } finally {
            if($job != null) {
                $this->service->releaseRunLock($job);
            }
        }

        return Command::SUCCESS;
    }


}