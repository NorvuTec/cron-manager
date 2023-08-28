<?php

namespace Norvutec\CronManagerBundle\Model;

use Cron\CronExpression;
use Norvutec\CronManagerBundle\Attribute\Cronjob;
use Symfony\Component\Console\Command\Command;

/**
 * Internal definition of a cronjob for service and management purposes
 */
class CronjobDefinition {

    public function __construct(
        private readonly Cronjob $cronjob,
        private readonly Command $commandController
    )
    {

    }

    public function getTag(): string {
        return $this->cronjob->getTag();
    }

    public function getName(): string {
        return $this->cronjob->getName();
    }

    public function getCommand(): string {
        return $this->commandController->getName();
    }

    public function getCronExpression(): CronExpression {
        return $this->cronjob->getCronSchedule();
    }

    public function getExecutionArgs(): array {
        return $this->cronjob->getCommandArgs();
    }

}