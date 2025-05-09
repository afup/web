<?php

declare(strict_types=1);

namespace AppBundle\Model;

use CCMBenchmark\Ting\Repository\CollectionInterface;

class CollectionFilter
{
    /**
     * @param $method
     * @param $value
     */
    public function filter(CollectionInterface $collection, $method, $value): array
    {
        $items = iterator_to_array($collection->getIterator());

        return array_filter($items, function ($item) use ($method, $value): bool {
            if (method_exists($item, $method) === false) {
                throw new \RuntimeException(sprintf('Could not find method "%s" on object of type "%s"', $method, $item::class));
            }
            return $item->$method() === $value;
        });
    }

    /**
     * @param $method
     * @param $value
     * @return Object|null
     */
    public function findOne(CollectionInterface $collection, $method, $value)
    {
        $extractedItems = $this->filter($collection, $method, $value);
        if (count($extractedItems) !== 1) {
            return null;
        }
        return current($extractedItems);
    }
}
