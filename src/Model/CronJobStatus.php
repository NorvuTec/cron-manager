<?php

namespace Norvutec\CronManagerBundle\Model;

use Norvutec\CronManagerBundle\Attribute\Cronjob;

/**
 * Execution status of a {@link Cronjob}
 */
enum CronJobStatus: string {
    case UNKNOWN = 'unknown';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case RUNNING = 'running';
    case SKIPPED = 'skipped';
}