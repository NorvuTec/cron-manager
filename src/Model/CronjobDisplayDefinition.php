<?php

namespace Norvutec\CronManagerBundle\Model;

use Norvutec\CronManagerBundle\Attribute\Cronjob;
use Norvutec\CronManagerBundle\Entity\CronJobHistory;
use Symfony\Component\Console\Command\Command;

class CronjobDisplayDefinition extends CronjobDefinition {

    private ?CronJobHistory $lastRun;

    public function __construct(Cronjob $cronjob, Command $commandController)
    {
        parent::__construct($cronjob, $commandController);
    }

    public static function of(CronjobDefinition $definition): CronjobDisplayDefinition {
        return new self($definition->cronjob, $definition->commandController);
    }

    public function getLastRun(): ?CronJobHistory
    {
        return $this->lastRun;
    }

    public function setLastRun(?CronJobHistory $lastRun): self
    {
        $this->lastRun = $lastRun;
        return $this;
    }


}