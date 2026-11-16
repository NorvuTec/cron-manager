<?php

declare(strict_types=1);

namespace Norvutec\CronManagerBundle\Tests\Unit\Service;

use Norvutec\CronManagerBundle\Attribute\Cronjob;
use Norvutec\CronManagerBundle\Model\CronjobDefinition;
use Norvutec\CronManagerBundle\Repository\CronJobHistoryRepository;
use Norvutec\CronManagerBundle\Service\CronManagerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\InMemoryStore;

final class CronManagerServiceLockTest extends TestCase
{
    public function testParallelFindNextClaimsDistinctJobs(): void
    {
        $store = new InMemoryStore();
        $history = $this->createMock(CronJobHistoryRepository::class);
        $history->method('getMappedLatestRuns')->willReturn([]);

        $first = $this->service(new LockFactory($store), $history);
        $second = $this->service(new LockFactory($store), $history);
        $this->registerJobs($first);
        $this->registerJobs($second);

        $claimedByFirst = $first->findNextCronjobForExecution();
        $claimedBySecond = $second->findNextCronjobForExecution();

        self::assertInstanceOf(CronjobDefinition::class, $claimedByFirst);
        self::assertInstanceOf(CronjobDefinition::class, $claimedBySecond);
        self::assertNotSame($claimedByFirst->getTag(), $claimedBySecond->getTag());
    }

    public function testReleaseAllowsAnotherRunnerToClaimSameTag(): void
    {
        $store = new InMemoryStore();
        $history = $this->createMock(CronJobHistoryRepository::class);
        $history->method('getMappedLatestRuns')->willReturn([]);
        $history->method('getLastCompleted')->willReturn(null);

        $holder = $this->service(new LockFactory($store), $history);
        $waiter = $this->service(new LockFactory($store), $history);
        $this->registerJobs($holder);
        $this->registerJobs($waiter);

        $job = $holder->findCronjobForExecution('cron_lock_job_a', true);
        self::assertInstanceOf(CronjobDefinition::class, $job);
        self::assertNull($waiter->findCronjobForExecution('cron_lock_job_a'));

        $holder->releaseRunLock($job);

        $reclaimed = $waiter->findCronjobForExecution('cron_lock_job_a');
        self::assertInstanceOf(CronjobDefinition::class, $reclaimed);
        self::assertSame('cron_lock_job_a', $reclaimed->getTag());
    }

    public function testClaimHoldsLockAcrossReturn(): void
    {
        $store = new InMemoryStore();
        $history = $this->createMock(CronJobHistoryRepository::class);
        $service = $this->service(new LockFactory($store), $history);
        $probe = new LockFactory($store);
        $this->registerJobs($service);

        $jobs = $service->getCronjobs();
        self::assertFalse($jobs->isEmpty());
        /** @var CronjobDefinition $job */
        $job = $jobs->first();

        self::assertTrue($service->claimRunLock($job));
        // Held instance must survive claimRunLock() return (autoRelease=false + store on service).
        self::assertFalse($probe->createLock('cronmanager:job:'.$job->getTag(), 300.0, false)->acquire());

        $service->releaseRunLock($job);
        self::assertTrue($probe->createLock('cronmanager:job:'.$job->getTag(), 300.0, false)->acquire());
    }

    public function testClaimIsIdempotentWhileHeld(): void
    {
        $store = new InMemoryStore();
        $history = $this->createMock(CronJobHistoryRepository::class);
        $service = $this->service(new LockFactory($store), $history);
        $this->registerJobs($service);

        /** @var CronjobDefinition $job */
        $job = $service->getCronjobs()->first();

        self::assertTrue($service->claimRunLock($job));
        self::assertTrue($service->claimRunLock($job));

        $service->releaseRunLock($job);
    }

    private function service(LockFactory $factory, CronJobHistoryRepository $history): CronManagerService
    {
        return new CronManagerService($factory, $history);
    }

    private function registerJobs(CronManagerService $service): void
    {
        $service->addCronjobService(new CronLockJobACommand());
        $service->addCronjobService(new CronLockJobBCommand());
    }
}

#[AsCommand(name: 'test:cron-lock-job-a')]
#[Cronjob('cron_lock_job_a', 'Cron lock job A', '* * * * *')]
class CronLockJobACommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}

#[AsCommand(name: 'test:cron-lock-job-b')]
#[Cronjob('cron_lock_job_b', 'Cron lock job B', '* * * * *')]
class CronLockJobBCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
