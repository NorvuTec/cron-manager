<?php

namespace Norvutec\CronManagerBundle\Model\Exception;

class InvalidCronExpressionException extends CronManagerException {

    public function __construct(
        private string $cronExpression
    )
    {
        parent::__construct("Invalid cron expression: " . $this->cronExpression);
    }

}