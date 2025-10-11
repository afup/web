<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\Event;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Event>
 */
class EventRepository extends Repository implements MetadataInitializer
{
    /**
     * @return CollectionInterface<Event>
     */
    public function getAllSortedByName(): CollectionInterface
    {
        $query = $this->getQuery('SELECT * FROM compta_evenement ORDER BY evenement asc');
        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Event::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('compta_evenement');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'evenement',
                'fieldName' => 'name',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'hide_in_accounting_journal_at',
                'fieldName' => 'hideInAccountingJournalAt',
                'type' => 'datetime',
            ])
        ;

        return $metadata;
    }
}
