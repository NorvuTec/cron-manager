<?php

namespace Norvutec\CronManagerBundle\Attribute;

use Attribute;
use Norvutec\CronManagerBundle\Model\Exception\DuplicateCronjobTagException;
use Norvutec\CronManagerBundle\Model\Exception\InvalidCronjobTagException;

/**
 *
 * @Annotation
 * @Target("CLASS")
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Cronjob {

    private static array $knownTags = [];

    public function __construct(
        private string $tag, /* Unique identifier */
        private string $name,
        private string $cronExpression,
        private ?array $commandArgs = null
    ) {
        if(in_array($this->tag, self::$knownTags)) {
            throw new DuplicateCronjobTagException($this->tag);
        }
        if(strlen($this->tag) > 50) {
            throw new InvalidCronjobTagException($this->tag);
        }
        self::$knownTags[] = $this->tag;
    }

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