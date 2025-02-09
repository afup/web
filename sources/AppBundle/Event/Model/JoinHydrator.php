<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

class JoinHydrator extends HydratorAggregator
{
    public function aggregateOn($mainObjectAlias, $joinedObjectAlias, $mainObjectGetter): self
    {
        $this
            ->callableDataIs(fn ($result) => $result[$joinedObjectAlias])
            ->callableIdIs(fn ($result) => $result[$mainObjectAlias]->$mainObjectGetter())
            ->callableFinalizeAggregate(function (array $result, $aggregate) use ($joinedObjectAlias) {
                $result['.aggregation'][$joinedObjectAlias] = array_filter($aggregate);
                return $result;
            })
        ;
        return $this;
    }
}
