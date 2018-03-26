<?php


namespace AppBundle\TechLetter\Model\Repository;

use AppBundle\TechLetter\Model\Sending;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class SendingRepository extends Repository implements MetadataInitializer
{
    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Sending::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_techletter');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'sending_date',
                'fieldName' => 'sendingDate',
                'type' => 'datetime'
            ])
            ->addField([
                'columnName' => 'techletter',
                'fieldName' => 'techletter',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'sent_to_mailchimp',
                'fieldName' => 'sentToMailchimp',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
        ;

        return $metadata;
    }
}
