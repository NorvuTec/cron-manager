<?php

namespace Norvutec\CronManagerBundle\Model\Exception;

/**
 * Exception if there is a {@link Cronjob} tag defined multiple times.
 * @package Norvutec\CronManagerBundle\Model
 */
class DuplicateCronjobTagException extends CronManagerException {

    public function __construct(private readonly string $tag) {
        parent::__construct("Duplicate tag '$tag' found in cronjob definitions.");
    }

    /**
     * Returns the duplicated tag
     * @return string tag
     */
    public function getTag(): string {
        return $this->tag;
    }

}