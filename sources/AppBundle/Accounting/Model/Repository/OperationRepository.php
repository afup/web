<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Model\Repository;

use AppBundle\Accounting\Model\Operation;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Operation>
 */
class OperationRepository extends Repository implements MetadataInitializer
{
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Operation::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('compta_operation');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'operation',
                'fieldName' => 'name',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
