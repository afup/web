<?php

namespace AppBundle\Event\Model;

class JoinHydrator extends HydratorAggregator
{
    public function aggregateOn($mainObjectAlias, $joinedObjectAlias, $mainObjectGetter)
    {
        $this
            ->callableDataIs(fn ($result) => $result[$joinedObjectAlias])
            ->callableIdIs(fn ($result) => $result[$mainObjectAlias]->$mainObjectGetter())
            ->callableFinalizeAggregate(function ($result, $aggregate) use ($joinedObjectAlias) {
                $result['.aggregation'][$joinedObjectAlias] = array_filter($aggregate);
                return $result;
            })
        ;
        return $this;
    }
}
