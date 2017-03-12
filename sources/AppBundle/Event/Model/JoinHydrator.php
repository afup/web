<?php


namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Exception;
use CCMBenchmark\Ting\Repository\Hydrator;

class JoinHydrator extends Hydrator
{
    private $nextResultRow = null;

    private $aggregation = [];

    public function aggregateOn($mainObjectAlias, $joinedObjectAlias, $mainObjectGetter)
    {
        $this->aggregation = [
            'mainObjectAlias' => $mainObjectAlias,
            'joinedObjectAlias' => $joinedObjectAlias,
            'mainObjectGetter' => $mainObjectGetter
        ];

        return $this;
    }

    public function count()
    {
        throw new \RuntimeException('You cannot count results for a JoinHydrator, as results are built on loop');
    }

    /**
     * @return \Generator
     * @throws Exception
     */
    public function getIterator()
    {
        $aggregationChecked = false;

        foreach ($this->result as $key => $columns) {
            $result = $this->hydrateColumns(
                $this->result->getConnectionName(),
                $this->result->getDatabase(),
                $columns
            );
            if ($aggregationChecked === false) {
                if (isset($result[$this->aggregation['mainObjectAlias']]) === false) {
                    throw new Exception(sprintf(
                            'Your resultset does not include the alias "%s" but an aggregation has been defined for it. ' .
                            'Is there a typo ?',
                            $this->aggregation['mainObjectAlias']
                        )
                    );
                }
                if (
                    method_exists(
                        $result[$this->aggregation['mainObjectAlias']],
                        $this->aggregation['mainObjectGetter']
                    ) === false
                ) {
                    throw new Exception(sprintf(
                            'Your alias %s does not include the getter "%s" but an aggregation has been defined for it. ' .
                            'Is there a typo ?',
                            $this->aggregation['mainObjectAlias'],
                            $this->aggregation['mainObjectGetter']
                        )
                    );
                }
                $aggregationChecked = true;
            }

            if ($this->nextResultRow !== null) {
                if (isset($this->nextResultRow[$this->aggregation['mainObjectAlias']]) === true) {
                    $nextResultId = $this->nextResultRow[$this->aggregation['mainObjectAlias']]->{$this->aggregation['mainObjectGetter']}();
                    $resultId = $result[$this->aggregation['mainObjectAlias']]->{$this->aggregation['mainObjectGetter']}();

                    if ($nextResultId === $resultId) {
                        if (isset($this->nextResultRow['.aggregation'][$this->aggregation['joinedObjectAlias']]) === false) {
                            $this->nextResultRow['.aggregation'][$this->aggregation['joinedObjectAlias']] = [$this->nextResultRow[$this->aggregation['joinedObjectAlias']]];
                        }
                        $this->nextResultRow['.aggregation'][$this->aggregation['joinedObjectAlias']][] = $result[$this->aggregation['joinedObjectAlias']];
                        continue;
                    } else {
                        $toYield = $this->nextResultRow;

                        $this->nextResultRow = $this->prepareNextResultRow($result);

                        yield $key => $toYield;
                    }
                } else {
                    throw new Exception(sprintf('Could not find alias "%s" on result', $this->aggregation['mainObjectAlias']));
                }
            }
            $this->nextResultRow = $this->prepareNextResultRow($result);
        }
        if ($this->nextResultRow !== null && isset($key)) {
            yield $key => $this->nextResultRow;
        }
    }

    private function prepareNextResultRow($row)
    {
        $row['.aggregation'][$this->aggregation['joinedObjectAlias']] = [];
        if ($row[$this->aggregation['joinedObjectAlias']] !== null) {
            $row['.aggregation'][$this->aggregation['joinedObjectAlias']][] = $row[$this->aggregation['joinedObjectAlias']];
        }
        unset($row[$this->aggregation['joinedObjectAlias']]);

        return $row;
    }
}
