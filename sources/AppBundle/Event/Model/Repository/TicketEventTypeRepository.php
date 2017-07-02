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
    public function getTicketsByEvent(Event $event, $publicOnly = true, $actualTickets = true)
    {
        $sql = '
            SELECT
            id_tarif, id_event, price, date_start, date_end, description,
            tarif.id, tarif.technical_name, tarif.day, tarif.pretty_name, tarif.public, tarif.members_only, tarif.default_price, tarif.active, tarif.cfp_submitter_only
            FROM afup_forum_tarif_event tarif_event
            JOIN afup_forum_tarif tarif ON tarif.id = tarif_event.id_tarif
            WHERE id_event = :event 
        ';

        $params = ['event' => $event->getId()];
        if ($actualTickets === true) {
            $sql .= ' AND date_start < NOW() AND date_end > NOW() ';
        }
        if ($publicOnly === true) {
            $sql .= 'AND public = :public';
            $params['public'] = $publicOnly;
        }
        $sql .='
            ORDER BY date_start, date_end, price, tarif.members_only DESC
        ';
        $query = $this->getPreparedQuery($sql)->setParams($params);

        return $query->query(
            $this->getCollection(
                (new HydratorSingleObject())->mapObjectTo('tarif', 'tarif_event', 'setTicketType')
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
