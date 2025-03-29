<?php

declare(strict_types=1);


namespace AppBundle\Validator\Constraints;

use CCMBenchmark\Ting\Repository\Repository;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueEntity
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueEntity extends Constraint
{
    public string $message = 'Another entity exists for this data: {{ data }}';
    public Repository $repository;

    /** @var array<string> */
    public array $fields = [];

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getRequiredOptions(): array
    {
        return ['fields', 'repository'];
    }
}
