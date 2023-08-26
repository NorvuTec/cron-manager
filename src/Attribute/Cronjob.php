<?php

namespace Norvutec\CronManagerBundle\Attribute;

use Attribute;

/**
 *
 * @Annotation
 * @Target("CLASS")
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Cronjob {

    public function __construct(
        private string $name
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

}