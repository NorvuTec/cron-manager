<?php

namespace Norvutec\CronManagerBundle\Model;

use Cron\Schedule\CrontabSchedule;

class CronjobSchedule extends CrontabSchedule {

    public function __construct(
        private readonly string $pattern
    ) {
        parent::__construct($pattern);
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getLastRequiredExecutionTime(\DateTime $before = new \DateTime()) : ?\DateTime {
        // TODO Calculate last required execution time
        return new \DateTime();
    }

    public function getNextRequiredExecutionTime(\DateTime $after = new \DateTime()) : ?\DateTime {
        // TODO Calculate next required execution time
        return new \DateTime();
    }

}