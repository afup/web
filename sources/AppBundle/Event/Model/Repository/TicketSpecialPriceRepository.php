<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\JoinHydrator;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketSpecialPrice;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TicketSpecialPriceRepository extends Repository implements MetadataInitializer
{
    /**
     * @param string $token
     * @return TicketSpecialPrice
     */
    public function findUnusedToken(Event $event, $token)
    {
        $query = $this
            ->getPreparedQuery(
                'SELECT afup_forum_special_price.*
            FROM afup_forum_special_price
            LEFT JOIN (
              SELECT DISTINCT afup_inscription_forum.special_price_token as used_token
              FROM afup_inscription_forum
              WHERE afup_inscription_forum.etat <> :etat_annule
            ) as used_tokens ON (afup_forum_special_price.token = used_tokens.used_token)
            WHERE afup_forum_special_price.token = :token
              AND used_tokens.used_token IS NULL
              AND afup_forum_special_price.date_start <= NOW()
              AND afup_forum_special_price.date_end >= NOW()
              AND afup_forum_special_price.id_event = :id_event
            LIMIT 1
            ')
            ->setParams(['token' => $token, 'id_event' => $event->getId(), 'etat_annule' => Ticket::STATUS_CANCELLED])
        ;
        return $query->query($this->getCollection(new HydratorSingleObject()))->first();
    }

    /**
     * @return CollectionInterface
     */
    public function getByEvent(Event $event)
    {
        $hydrator = new JoinHydrator();
        $hydrator->aggregateOn('special_price', 'inscription', 'getId');

        $query = $this->getPreparedQuery(
            'SELECT special_price.*, inscription.*, creator.*
            FROM afup_forum_special_price as special_price
            LEFT JOIN afup_inscription_forum as inscription ON (special_price.token = inscription.special_price_token AND inscription.etat <> :etat_annule)
            LEFT JOIN afup_personnes_physiques as creator ON (special_price.creator_id = creator.id)
            WHERE special_price.id_event = :id_event
            ORDER BY special_price.id_event, special_price.id DESC, inscription.id DESC
            '
        )->setParams(['id_event' => $event->getId(), 'etat_annule' => Ticket::STATUS_CANCELLED]);

        return $query->query($this->getCollection($hydrator));
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(TicketSpecialPrice::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_forum_special_price');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_event',
                'fieldName' => 'eventId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'token',
                'fieldName' => 'token',
                'type' => 'string',
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
                'columnName' => 'created_on',
                'fieldName' => 'createdOn',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'creator_id',
                'fieldName' => 'creatorId',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
