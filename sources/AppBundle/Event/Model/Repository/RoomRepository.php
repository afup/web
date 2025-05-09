<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Room;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class RoomRepository extends Repository implements MetadataInitializer
{
    /**
     * @return CollectionInterface
     */
    public function getByEvent(Event $event)
    {
        return $this->getBy(['eventId' => $event->getId()]);
    }

    /**
     * @return Metadata
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Room::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum_salle');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'name',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
