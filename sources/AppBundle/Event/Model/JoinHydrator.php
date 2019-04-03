<?php

namespace AppBundle\Event\Model;

class JoinHydrator extends HydratorAggregator
{
    public function aggregateOn($mainObjectAlias, $joinedObjectAlias, $mainObjectGetter)
    {
        $this
            ->callableDataIs(function ($result) use ($joinedObjectAlias) {
                return $result[$joinedObjectAlias];
            })
            ->callableIdIs(function ($result) use ($mainObjectAlias, $mainObjectGetter) {
                return $result[$mainObjectAlias]->$mainObjectGetter();
            })
            ->callableFinalizeAggregate(function ($result, $aggregate) use ($joinedObjectAlias) {
                $result['.aggregation'][$joinedObjectAlias] = array_filter($aggregate);
                return $result;
            })
        ;
        return $this;
    }
}
