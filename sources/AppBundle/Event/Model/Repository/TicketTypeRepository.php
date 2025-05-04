<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\TicketType;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TicketTypeRepository extends Repository implements MetadataInitializer
{
    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(TicketType::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum_tarif');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'technical_name',
                'fieldName' => 'technicalName',
                'type' => 'string',
            ])

            ->addField([
                'columnName' => 'pretty_name',
                'fieldName' => 'prettyName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'public',
                'fieldName' => 'isPublic',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'members_only',
                'fieldName' => 'isRestrictedToMembers',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'cfp_submitter_only',
                'fieldName' => 'isRestrictedToCfpSubmitter',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'default_price',
                'fieldName' => 'defaultPrice',
                'type' => 'float',
            ])
            ->addField([
                'columnName' => 'active',
                'fieldName' => 'isActive',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'day',
                'fieldName' => 'day',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
