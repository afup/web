<?php

namespace App\Ting;

use CCMBenchmark\Ting\Query\Query;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Repository;

class TingHelper
{
    /** @return object|null */
    public static function getOneOrNullResult(Repository $repository, Query $query)
    {
        $results = $query->query($repository->getCollection(new HydratorSingleObject()));
        if (0 === $results->count()) {
            return null;
        }

        return $results->first();
    }

    public static function getResult(Repository $repository, Query $query)
    {
        dump($query);
        return $query->query($repository->getCollection(new HydratorSingleObject()));
    }
}
