<?php

namespace Norvutec\CronManagerBundle\Model;

/**
 * Exception if there is a {@link Cronjob} tag defined multiple times.
 * @package Norvutec\CronManagerBundle\Model
 */
class DuplicateCronjobTagException extends \Exception {

    public function __construct(private readonly string $tag) {
        parent::__construct("[Cron-Manager] Duplicate tag '$tag' found in cronjob definitions.");
    }

    /**
     * Returns the duplicated tag
     * @return string tag
     */
    public function getTag(): string {
        return $this->tag;
    }

}