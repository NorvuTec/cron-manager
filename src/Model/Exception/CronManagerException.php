<?php

namespace Norvutec\CronManagerBundle\Model\Exception;

abstract class CronManagerException extends \Exception {

        public function __construct(string $message) {
            parent::__construct("[Cron-Manager] $message");
        }

}