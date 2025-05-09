<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\SpeakerSuggestion;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class SpeakerSuggestionRepository extends Repository implements MetadataInitializer
{
    /**
     *
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(SpeakerSuggestion::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_speaker_suggestion');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'event_id',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'suggester_email',
                'fieldName' => 'suggesterEmail',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'suggester_name',
                'fieldName' => 'suggesterName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'speaker_name',
                'fieldName' => 'speakerName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'comment',
                'fieldName' => 'comment',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'created_at',
                'fieldName' => 'createdAt',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false],
                ],
            ])
        ;

        return $metadata;
    }
}
