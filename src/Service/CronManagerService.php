<?php

namespace Norvutec\CronManagerBundle\Service;

use Cron\Cron;
use Doctrine\Common\Collections\ArrayCollection;
use Norvutec\CronManagerBundle\Attribute\Cronjob;
use Norvutec\CronManagerBundle\Model\CronjobDefinition;
use Norvutec\CronManagerBundle\Model\Exception\CronjobNotFoundException;
use Norvutec\CronManagerBundle\Model\Exception\UnableToForceLockJobException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\LockFactory;

/**
 * Service for managing the known {@link Cronjob}s and the execution of them
 */
#[AutoconfigureTag("norvutec.cron_manager_bundle.service")]
class CronManagerService {

    /**
     * All known cronjobs of the system
     * @var ArrayCollection<CronjobDefinition>
     */
    private ArrayCollection $cronjobs;

    public function __construct(
        private readonly LockFactory $cronmanagerLockFactory
    ) {
        $this->cronjobs = new ArrayCollection();
    }

    /**
     * Method for {@link NorvuTecCronManagerCompilerPass} adding tagged services
     * @param object $commandController The command controller
     * @param array $tags Tags of the service
     * @return void
     */
    public function addCronjobService(object $commandController, array $tags): void {
        if(!($commandController instanceof Command)) {
            return;
        }
        $reflection = new \ReflectionClass($commandController);
        $cronAttributes = $reflection->getAttributes(Cronjob::class);
        if(count($cronAttributes) == 0) {
            return;
        }
        foreach($cronAttributes as $cronAttribute) {
            $this->cronjobs->add(new CronjobDefinition($cronAttribute->newInstance(), $commandController));
        }
    }

    /**
     * Gets all known cronjobs of the system
     * @return ArrayCollection<CronjobDefinition>
     */
    public function getCronjobs(): ArrayCollection {
        return $this->cronjobs;
    }

    /**
     * Searches for the given cronjob and tries to lock it for execution
     * @param string $tag Tag of the cronjob
     * @param bool $force Force the execution
     * @return CronjobDefinition|null The cronjob if found and locked, null otherwise
     * @throws CronjobNotFoundException
     * @throws UnableToForceLockJobException
     */
    public function findCronjobForExecution(string $tag, bool $force = false): ?CronjobDefinition {
        $cronjob = $this->cronjobs->filter(function(CronjobDefinition $cronjob) use ($tag) {
            return $cronjob->getTag() == $tag;
        })->first();
        if(!$cronjob) {
            throw new CronjobNotFoundException($tag);
        }
        /** @var CronjobDefinition $cronjob */
        if(!$this->isOnDue($cronjob) && !$force) {
            return null;
        }
        if(!$this->claimRunLock($cronjob)) {
            if($force) {
                throw new UnableToForceLockJobException($tag);
            }
            return null;
        }
        return $cronjob;
    }

    /**
     * Searches for the next job needs to be executed
     * @return CronjobDefinition|null
     */
    public function findNextCronjobForExecution(): ?CronjobDefinition {


        if(!$this->claimRunLock($job)) {
            return null;
        }
        return null;
    }

    /**
     * Locks the job for execution
     * @param CronjobDefinition $job Job to lock
     * @return bool was locked for this process
     */
    public function claimRunLock(CronjobDefinition $job): bool {
        $jobLock = $this->getLock($job);
        if(!$jobLock->acquire()) {
            return false;
        }
        if(!$jobLock->isAcquired()) {
            return false;
        }
        return true;
    }

    /**
     * Releases the lock of the job
     * @param CronjobDefinition $job Job to release
     * @return void
     */
    public function releaseRunLock(CronjobDefinition $job): void {
        $this->getLock($job)->release();
    }

    /**
     * Gets the lock object of the {@link LockFactory}
     * @param CronjobDefinition $job Job to get the lock for
     * @return Lock The lock object
     */
    private function getLock(CronjobDefinition $job): Lock {
        return $this->cronmanagerLockFactory->createLock(
            "cronmanager:job:{$job->getTag()}"
        );
    }

    /**
     * Checks if the job is on due
     * @param CronjobDefinition $job Job to check
     * @return bool Is on due
     */
    public function isOnDue(CronjobDefinition $job): bool {
        return true;
    }


}