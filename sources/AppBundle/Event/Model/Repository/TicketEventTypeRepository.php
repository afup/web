<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\TicketEventType;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TicketEventTypeRepository extends Repository implements MetadataInitializer
{
    public function getTicketsByEvent(Event $event, $public = true)
    {
        /**
         * @todo check stock !
         */
        $query = $this->getPreparedQuery('
            SELECT
            id_tarif, id_event, price, date_start, date_end, description,
            aft.id, aft.technical_name, aft.day, aft.pretty_name, aft.public, aft.members_only, aft.default_price, aft.active
            FROM afup_forum_tarif_event afte
            JOIN afup_forum_tarif aft ON aft.id = afte.id_tarif
            WHERE date_start < NOW() AND date_end > NOW()
            AND id_event = :event
            AND public = :public
            ORDER BY date_start, date_end
        ')->setParams([
            'event' => $event->getId(),
            'public' => $public
        ]);

        return $query->query(
            $this->getCollection(
                (new HydratorSingleObject())->mapObjectTo('aft', 'afte', 'setTicketType')
            )
        );
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(TicketEventType::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum_tarif_event');

        $metadata
            ->addField([
                'columnName' => 'id_tarif',
                'fieldName' => 'ticketTypeId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'id_event',
                'fieldName' => 'eventId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'price',
                'fieldName' => 'price',
                'type' => 'float'
            ])
            ->addField([
                'columnName' => 'date_start',
                'fieldName' => 'dateStart',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'date_end',
                'fieldName' => 'dateEnd',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'description',
                'fieldName' => 'description',
                'type' => 'string'
            ])
        ;

        return $metadata;
    }
}
