<?php

namespace Norvutec\CronManagerBundle\Model;

enum CronJobStatus: string {
    case UNKNOWN = 'unknown';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case RUNNING = 'running';
    case SKIPPED = 'skipped';
}