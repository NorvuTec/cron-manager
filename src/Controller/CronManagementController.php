<?php

namespace Norvutec\CronManagerBundle\Controller;

use Norvutec\CronManagerBundle\Model\CronjobDefinition;
use Norvutec\CronManagerBundle\Model\CronjobDisplayDefinition;
use Norvutec\CronManagerBundle\Repository\CronJobHistoryRepository;
use Norvutec\CronManagerBundle\Service\CronManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CronManagementController extends AbstractController
{

    public function __construct(
        private readonly CronManagerService         $service,
        private readonly CronJobHistoryRepository   $historyRepository
    ) {}

    #[Route('/', name: 'index')]
    public function index(): Response {
        $cronjobs = $this->service->getCronjobs();
        $displayCronJobs = [];
        /** @var CronjobDefinition $cronjob */
        foreach($cronjobs as $cronjob) {
            $displayCron = CronjobDisplayDefinition::of($cronjob)
                ->setLastRun($this->historyRepository->getLastCompleted($cronjob->getTag()));
            $displayCronJobs[] = $displayCron;
        }

        return $this->render('@NorvutecCronManagerBundle/CronManagement/index.html.twig', [
            "cronjobs" => $displayCronJobs
        ]);
    }

    #[Route('/details/{tag}', name: 'details')]
    public function details(): Response {

    }


}