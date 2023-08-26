<?php

namespace Norvutec\CronManagerBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Norvutec\CronManagerBundle\Attribute\Cronjob;
use Norvutec\CronManagerBundle\Model\CronjobDefinition;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

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

    public function __construct() {
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

}