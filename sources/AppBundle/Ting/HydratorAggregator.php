<?php

declare(strict_types=1);
/***********************************************************************
 *
 * Ting - PHP Datamapper
 * ==========================================
 *
 * Copyright (C) 2014 CCM Benchmark Group. (http://www.ccmbenchmark.com)
 *
 ***********************************************************************
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you
 * may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 **********************************************************************/

namespace AppBundle\Ting;

use CCMBenchmark\Ting\Repository\Hydrator;
use Closure;
use Generator;

class HydratorAggregator extends Hydrator
{
    private Closure $callableForId;
    private Closure $callableForData;
    private ?Closure $callableFinalizeAggregate = null;

    public function callableIdIs(Closure $callableForId): static
    {
        $this->callableForId = $callableForId;
        return $this;
    }

    public function callableDataIs(Closure $callableForData): static
    {
        $this->callableForData = $callableForData;
        return $this;
    }

    public function callableFinalizeAggregate(Closure $callableFinalizeAggregate): static
    {
        $this->callableFinalizeAggregate = $callableFinalizeAggregate;
        return $this;
    }

    /**
     * @return Generator
     */
    public function getIterator(): Generator
    {
        $knownIdentifiers = [];
        $callableForId = $this->callableForId;
        $callableForData = $this->callableForData;
        $previousId = null;
        $previousResult = null;
        $previousKey = null;
        $currentId = null;
        $aggregate = [];
        $key = null;
        $result = null;

        foreach ($this->result as $key => $columns) {
            $result = $this->hydrateColumns(
                $this->result->getConnectionName(),
                $this->result->getDatabase(),
                $columns,
            );

            $currentId = $callableForId($result);

            if (isset($knownIdentifiers[$currentId])) {
                continue;
            }

            if ($previousId === null) {
                $previousId = $currentId;
                $previousResult = $result;
                $previousKey = $key;
            }

            if ($previousId === $currentId) {
                $aggregate[] = $callableForData($result);
            } else {
                $previousResult = $this->finalizeAggregate($previousResult, $aggregate);

                $knownIdentifiers[$previousId] = true;

                yield $previousKey => $previousResult;

                $aggregate = [$callableForData($result)];
                $previousId = $currentId;
                $previousResult = $result;
                $previousKey = $key;
            }
        }

        if ($previousId === $currentId && $previousId !== null) {
            yield $key => $this->finalizeAggregate($result, $aggregate);
        }
    }

    private function finalizeAggregate(array $result, $aggregate): array
    {
        if ($this->callableFinalizeAggregate === null) {
            $result['aggregate'] = $aggregate;
            return $result;
        }

        $callableFinalizeAggregate = $this->callableFinalizeAggregate;
        return $callableFinalizeAggregate($result, $aggregate);
    }
}
