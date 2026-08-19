<?php

namespace Norvutec\CronManagerBundle\Attribute;

use Attribute;
use Cron\CronExpression;
use InvalidArgumentException;
use Norvutec\CronManagerBundle\Model\Exception\InvalidCronExpressionException;
use Norvutec\CronManagerBundle\Model\Exception\InvalidCronjobTagException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Cronjob {

    private CronExpression $cronSchedule;

    /**
     * @throws InvalidCronjobTagException
     * @throws InvalidCronExpressionException
     */
    public function __construct(
        private readonly string $tag, /* Unique identifier */
        private readonly string $name,
        string                  $cronExpression,
        private readonly ?array $commandArgs = null
    ) {
        if(strlen($this->tag) > 50) {
            throw new InvalidCronjobTagException($this->tag);
        }
        try {
            $this->cronSchedule = new CronExpression($cronExpression);
        } catch (InvalidArgumentException $e) {
            throw new InvalidCronExpressionException($cronExpression);
        }
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CronExpression
     */
    public function getCronSchedule(): CronExpression
    {
        return $this->cronSchedule;
    }

    public function getCommandArgs(): ?array
    {
        return $this->commandArgs;
    }

}