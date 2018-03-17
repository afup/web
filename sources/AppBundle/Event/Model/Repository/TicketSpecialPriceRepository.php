<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\TicketSpecialPrice;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TicketSpecialPriceRepository extends Repository implements MetadataInitializer
{
    /**
     * @param Event $event
     * @param string $token
     *
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
            ) as used_tokens ON (afup_forum_special_price.token = used_tokens.used_token)
            WHERE afup_forum_special_price.token = :token
              AND used_tokens.used_token IS NULL
              AND afup_forum_special_price.date_start <= NOW()
              AND afup_forum_special_price.date_end >= NOW()
              AND afup_forum_special_price.id_event = :id_event
            LIMIT 1
            ')
            ->setParams(['token' => $token, 'id_event' => $event->getId()])
        ;
        return $query->query($this->getCollection(new HydratorSingleObject()))->first();
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
