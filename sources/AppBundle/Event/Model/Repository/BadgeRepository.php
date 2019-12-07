<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Badge;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class BadgeRepository extends Repository implements MetadataInitializer
{
    /**
     * @param SerializerFactoryInterface $serializerFactory
     * @param array $options
     *
     * @return Metadata
     *
     * @throws \CCMBenchmark\Ting\Exception
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Badge::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_badge');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'label',
                'fieldName' => 'label',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'url',
                'fieldName' => 'url',
                'type' => 'string'
            ])
        ;

        return $metadata;
    }
}
