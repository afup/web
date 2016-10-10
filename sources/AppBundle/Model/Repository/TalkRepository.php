<?php


namespace AppBundle\Model\Repository;


use AppBundle\Model\Event;
use AppBundle\Model\Talk;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\Collection;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TalkRepository extends Repository implements MetadataInitializer
{
    public function getTalksToRateByEvent(Event $event, $limit = 10)
    {
        $query = $this->getPreparedQuery(
            'SELECT session_id, titre, abstract, id_forum
            FROM afup_sessions
            WHERE plannifie = 0 AND id_forum = :event
            ORDER BY RAND()
            LIMIT 0, 10
            '
        )->setParams(['event' => $event->getId()]);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * @param SerializerFactoryInterface $serializerFactory
     * @param array $options
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Talk::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_sessions');

        $metadata
            ->addField([
                'columnName' => 'session_id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'forumId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'date_soumission',
                'fieldName' => 'submittedOn',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false]
                ]
            ])
            ->addField([
                'columnName' => 'titre',
                'fieldName' => 'title',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'abstract',
                'fieldName' => 'abstract',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'plannifie',
                'fieldName' => 'scheduled',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
        ;

        return $metadata;
    }

}
