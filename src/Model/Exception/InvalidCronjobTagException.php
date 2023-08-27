<?php

namespace Norvutec\CronManagerBundle\Model\Exception;

class InvalidCronjobTagException extends CronManagerException {

    public function __construct(string $tag) {
        parent::__construct("Invalid tag '$tag' found in cronjob definitions: tags must be within 50 characters, given ".strlen($tag));
    }

}