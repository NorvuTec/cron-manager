<?php

namespace Norvutec\CronManagerBundle\Attribute;

use Attribute;
use Cron\Exception\InvalidPatternException;
use Cron\Validator\CrontabValidator;
use Norvutec\CronManagerBundle\Model\CronjobSchedule;
use Norvutec\CronManagerBundle\Model\Exception\DuplicateCronjobTagException;
use Norvutec\CronManagerBundle\Model\Exception\InvalidCronExpressionException;
use Norvutec\CronManagerBundle\Model\Exception\InvalidCronjobTagException;

/**
 *
 * @Annotation
 * @Target("CLASS")
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Cronjob {

    private static array $knownTags = [];

    private CronjobSchedule $cronSchedule;

    /**
     * @throws InvalidCronjobTagException
     * @throws InvalidCronExpressionException
     * @throws DuplicateCronjobTagException
     */
    public function __construct(
        private string $tag, /* Unique identifier */
        private string $name,
        string $cronExpression,
        private ?array $commandArgs = null
    ) {
        if(in_array($this->tag, self::$knownTags)) {
            throw new DuplicateCronjobTagException($this->tag);
        }
        if(strlen($this->tag) > 50) {
            throw new InvalidCronjobTagException($this->tag);
        }
        self::$knownTags[] = $this->tag;
        $validator = new CrontabValidator();
        try {
            $this->cronSchedule = new CronjobSchedule($validator->validate($cronExpression));
        } catch (InvalidPatternException $e) {
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
     * @return CronjobSchedule
     */
    public function getCronSchedule(): CronjobSchedule
    {
        return $this->cronSchedule;
    }

    public function getCommandArgs(): ?array
    {
        return $this->commandArgs;
    }

}