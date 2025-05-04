<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Meetup;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Meetup>
 */
class MeetupRepository extends Repository implements MetadataInitializer
{
    /**
     *
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Meetup::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_meetup');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => false,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date',
                'fieldName' => 'date',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'title',
                'fieldName' => 'title',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'location',
                'fieldName' => 'location',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'description',
                'fieldName' => 'description',
                'type' => 'text',
                'nullable' => true,
            ])
            ->addField([
                'columnName' => 'antenne_name',
                'fieldName' => 'antenneName',
                'type' => 'string',
            ]);

        return $metadata;
    }
}
