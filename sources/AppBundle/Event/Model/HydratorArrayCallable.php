<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Repository\HydratorArray;
use Closure;

final class HydratorArrayCallable extends HydratorArray
{
    public function __construct(private readonly Closure $getIteratorClosure) {}

    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        foreach (parent::getIterator() as $key => $data) {
            ($this->getIteratorClosure)($key, $data);

            yield $key => $data;
        }
    }
}
