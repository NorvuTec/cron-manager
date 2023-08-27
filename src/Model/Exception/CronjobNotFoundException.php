<?php

namespace Norvutec\CronManagerBundle\Model\Exception;

class CronjobNotFoundException extends CronManagerException {

    public function __construct(private readonly string $tag) {
        parent::__construct("Cronjob $tag not found");
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

}