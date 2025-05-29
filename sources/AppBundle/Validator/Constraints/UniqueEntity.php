<?php

declare(strict_types=1);

namespace AppBundle\Validator\Constraints;

use CCMBenchmark\Ting\Repository\Repository;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class UniqueEntity extends Constraint
{
    /**
     * @param string[] $fields
     */
    public function __construct(
        public array $fields,
        public string|Repository $repository,
        public string $message = 'Another entity exists for this data: {{ data }}',
    ) {
        parent::__construct();
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
