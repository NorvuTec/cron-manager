<?php

namespace Norvutec\CronManagerBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Norvutec\CronManagerBundle\Entity\CronJobHistory;
use Norvutec\CronManagerBundle\Model\CronjobDefinition;
use Norvutec\CronManagerBundle\Repository\CronJobHistoryRepository;
use Norvutec\CronManagerBundle\Service\CronManagerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: "cron-manager:list",
    description: '[Cron-Manager] Lists all cronjobs'
)]
class CronManagerListCommand extends Command {

    public function __construct(
        private readonly CronManagerService         $cronManagerService,
        private readonly EntityManagerInterface     $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $table = new Table($output);
        $table->setHeaders(["Tag", "Name", "Command", "Last run", "Last completed", "Last error"]);

        /** @var CronJobHistoryRepository $cronJobRepository */
        $cronJobRepository = $this->entityManager->getRepository(CronJobHistory::class);

        /** @var CronjobDefinition $cronjob */
        foreach($this->cronManagerService->getCronjobs() as $cronjob) {
            $lastRun = $cronJobRepository->getLastCompleted($cronjob->getTag());
            $lastCompleted = $cronJobRepository->getLastSuccessful($cronjob->getTag());
            $lastFailed = $cronJobRepository->getLastFailed($cronjob->getTag());
            $table->addRow([
                $cronjob->getTag(),
                $cronjob->getName(),
                $cronjob->getCommand(),
                $lastRun ? $lastRun->getExitAt()->format("Y-m-d H:i:s") : "never",
                $lastCompleted ? $lastCompleted->getExitAt()->format("Y-m-d H:i:s") : "never",
                $lastFailed ? $lastFailed->getExitAt()->format("Y-m-d H:i:s") : "never"
            ]);
        }
        $table->render();
        return Command::SUCCESS;
    }

}