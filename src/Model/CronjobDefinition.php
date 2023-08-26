<?php

namespace Norvutec\CronManagerBundle\Model;

use Norvutec\CronManagerBundle\Attribute\Cronjob;
use Symfony\Component\Console\Command\Command;

class CronjobDefinition {

    public function __construct(
        private readonly Cronjob $cronjob,
        private readonly Command $commandController
    )
    {

    }

    public function getName(): string {
        return $this->cronjob->getName();
    }

    public function getCommand(): string {
        return $this->commandController->getName();
    }


}