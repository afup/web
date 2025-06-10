<?php

declare(strict_types=1);

namespace AppBundle\Ting;

class JoinHydrator extends HydratorAggregator
{
    public function aggregateOn(string $mainObjectAlias, string $joinedObjectAlias, string $mainObjectGetter): static
    {
        return $this
            ->callableDataIs(fn($result) => $result[$joinedObjectAlias])
            ->callableIdIs(fn($result) => $result[$mainObjectAlias]->$mainObjectGetter())
            ->callableFinalizeAggregate(function (array $result, $aggregate) use ($joinedObjectAlias) {
                $result['.aggregation'][$joinedObjectAlias] = array_filter($aggregate);
                return $result;
            })
        ;
    }
}
