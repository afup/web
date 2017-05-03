<?php

namespace AppBundle\Model;

use CCMBenchmark\Ting\Repository\CollectionInterface;

class CollectionFilter
{
    /**
     * @param CollectionInterface $collection
     * @param $method
     * @param $value
     * @return array
     */
    public function filter(CollectionInterface $collection, $method, $value)
    {
        $items = iterator_to_array($collection->getIterator());
        $items = array_filter($items, function ($item) use ($method, $value) {
            if (method_exists($item, $method) === false) {
                throw new \RuntimeException(sprintf('Could not find method "%s" on object of type "%s"', $method, get_class($item)));
            }
            if ($item->$method() === $value) {
                return true;
            }
            return false;
        });

        return $items;
    }

    /**
     * @param CollectionInterface $collection
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
        $item = current($extractedItems);
        return $item;
    }
}
