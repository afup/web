<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Entity\TicketEventType;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * Permet d'hydrater l'entité Doctrine TicketEventType (table afup_forum_tarif_event)
 * depuis une requête SQL brute via Ting : le repository Doctrine de TicketEventType
 * ne peut pas être injecté dans les repositories Ting (la factory Ting contrôle les arguments).
 *
 * @extends Repository<TicketEventType>
 */
class TicketEventTypeHydrationRepository extends Repository implements MetadataInitializer
{
    /**
     * @param SerializerFactoryInterface $serializerFactory
     * @param array<string, string> $options
     *
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
