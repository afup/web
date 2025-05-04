<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\TicketEventType;
use CCMBenchmark\Ting\Query\QueryException;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TicketEventTypeRepository extends Repository implements MetadataInitializer
{
    const REMOVE_PAST_TICKETS = 1;
    const REMOVE_FUTURE_TICKETS = 2;
    const ACTUAL_TICKETS_ONLY = 3; // Combination of REMOVE_PAST_TICKETS and REMOVE_FUTURE_TICKETS
    /**
     * @param bool $publicOnly
     * @param null|int $datesFilter can be one of self::REMOVE_PAST_TICKETS, self::REMOVE_FUTURE_TICKETS. self::ACTUAL_TICKETS == self::REMOVE_PAST_TICKETS | self::REMOVE_FUTURE_TICKETS. Default value is ACTUAL_TICKETS
     * @return CollectionInterface|TicketEventType[]
     * @throws QueryException
     */
    public function getTicketsByEvent(Event $event, $publicOnly = true, $datesFilter = null)
    {
        $sql = '
            SELECT
            id_tarif, id_event, price, date_start, date_end, description, tarif_event.max_tickets,
            tarif.id, tarif.technical_name, tarif.day, tarif.pretty_name, tarif.public, tarif.members_only, tarif.default_price, tarif.active, tarif.cfp_submitter_only
            FROM afup_forum_tarif_event tarif_event
            JOIN afup_forum_tarif tarif ON tarif.id = tarif_event.id_tarif
            WHERE id_event = :event 
        ';

        $params = ['event' => $event->getId()];

        if (($datesFilter & self::REMOVE_PAST_TICKETS) !== 0) {
            $sql .= ' AND date_end > NOW() ';
        }
        if (($datesFilter & self::REMOVE_FUTURE_TICKETS) !== 0) {
            $sql .= ' AND date_start < NOW() ';
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

    public function update(TicketEventType $ticketEventType)
    {
        $sql = 'UPDATE afup_forum_tarif_event
            SET price = :price,
                date_start = :date_start,
                date_end = :date_end,
                description = :description,
                max_tickets = :max_tickets
            WHERE id_tarif = :id_tarif AND id_event = :id_event';

        $query = $this->getPreparedQuery($sql)->setParams([
            'id_tarif' => $ticketEventType->getTicketTypeId(),
            'id_event' => $ticketEventType->getEventId(),
            'price' => $ticketEventType->getPrice(),
            'date_start' => $ticketEventType->getDateStart()->format('Y-m-d H:i:s'),
            'date_end' => $ticketEventType->getDateEnd()->format('Y-m-d H:i:s'),
            'description' => $ticketEventType->getDescription(),
            'max_tickets' => $ticketEventType->getMaxTickets(),
        ]);

        return $query->execute();
    }

    /**
     * @param bool $publicOnly
     *
     *
     * @throws QueryException
     */
    public function doesEventHasRestrictedToMembersTickets(Event $event, $publicOnly = true, $datesFilter = null): bool
    {
        $tickets = $this->getTicketsByEvent($event, $publicOnly, $datesFilter);

        foreach ($tickets as $ticket) {
            if ($ticket->getTicketType()->getIsRestrictedToMembers()) {
                return true;
            }
        }

        return false;
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
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_event',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'price',
                'fieldName' => 'price',
                'type' => 'float',
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
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'max_tickets',
                'fieldName' => 'maxTickets',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
