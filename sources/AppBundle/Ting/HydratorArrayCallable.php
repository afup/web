<?php

declare(strict_types=1);

namespace AppBundle\Ting;

use CCMBenchmark\Ting\Repository\HydratorArray;
use Closure;
use Generator;

final class HydratorArrayCallable extends HydratorArray
{
    public function __construct(private readonly Closure $getIteratorClosure) {}

    #[\ReturnTypeWillChange]
    public function getIterator(): Generator
    {
        foreach (parent::getIterator() as $key => $data) {
            ($this->getIteratorClosure)($key, $data);

            yield $key => $data;
        }
    }
}
