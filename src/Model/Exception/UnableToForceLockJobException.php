<?php

namespace Norvutec\CronManagerBundle\Model\Exception;

class UnableToForceLockJobException extends CronManagerException {

    public function __construct(string $tag) {
        parent::__construct("Unable to force lock job $tag");
    }

}