<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\Category;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Category>
 */
class CategoryRepository extends Repository implements MetadataInitializer
{
    /**
     * @return CollectionInterface<Category>
     */
    public function getAllSortedByName(): CollectionInterface
    {
        $query = $this->getQuery('SELECT * FROM compta_categorie ORDER BY categorie asc');
        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Category::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('compta_categorie');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'idevenement',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'categorie',
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
