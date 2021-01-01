<?php

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class GeneralMeetingQuestionRepository extends Repository implements MetadataInitializer
{
    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(GeneralMeetingQuestion::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_assemblee_generale_question');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date',
                'fieldName' => 'date',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U']
                ],
            ])
            ->addField([
                'columnName' => 'label',
                'fieldName' => 'label',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'opened_at',
                'fieldName' => 'openedAt',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'closed_at',
                'fieldName' => 'closedAt',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'created_at',
                'fieldName' => 'createdAt',
                'type' => 'datetime',
            ])
        ;

        return $metadata;
    }
}
