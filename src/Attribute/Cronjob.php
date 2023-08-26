<?php

namespace Norvutec\CronManagerBundle\Attribute;

use Attribute;

/**
 *
 * @Annotation
 * @Target("CLASS")
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Cronjob {

    public function __construct(
        private string $tag,
        private string $name,
        private string $cronExpression,
        private ?array $commandArgs = null
    ) {}

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCronExpression(): string
    {
        return $this->cronExpression;
    }

    public function getCommandArgs(): ?array
    {
        return $this->commandArgs;
    }

}