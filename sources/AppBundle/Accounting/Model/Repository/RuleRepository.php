<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\Rule;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Rule>
 */
class RuleRepository extends Repository implements MetadataInitializer
{
    /**
     * @return CollectionInterface<Rule>
     */
    public function getAllSortedByName(): CollectionInterface
    {
        $query = $this->getQuery('SELECT * FROM compta_regle ORDER BY label asc');
        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Rule::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('compta_regle');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'label',
                'fieldName' => 'label',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'condition',
                'fieldName' => 'condition',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'is_credit',
                'fieldName' => 'isCredit',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'mode_regl_id',
                'fieldName' => 'paymentTypeId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'vat',
                'fieldName' => 'vat',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'category_id',
                'fieldName' => 'categoryId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'event_id',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'attachment_required',
                'fieldName' => 'attachmentRequired',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
        ;

        return $metadata;
    }
}
