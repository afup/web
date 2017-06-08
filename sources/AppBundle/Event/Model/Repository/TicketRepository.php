<?php

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Ticket;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TicketRepository extends Repository implements MetadataInitializer
{
    public function getByReference($reference)
    {
        return $this->getBy(['reference' => $reference]);
    }

    public function getPublicSoldTicketsByDay($day, Event $event)
    {
        $tickets = $this->getPreparedQuery('
            SELECT COUNT(aif.id) AS sold_tickets
            FROM afup_inscription_forum aif
            JOIN afup_forum_tarif aft ON aft.id = aif.type_inscription
            WHERE aif.id_forum = :event AND aft.public = 1 AND FIND_IN_SET(:day, aft.day) > 0
        ')
            ->setParams(['event' => $event->getId(), 'day' => $day])
            ->query($this->getCollection(new HydratorArray()))
        ;
        if ($tickets === null) {
            return 0;
        }

        return $tickets->first()['sold_tickets'];
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(Ticket::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_inscription_forum');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'date',
                'fieldName' => 'date',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['format' => 'U'],
                ]
            ])
            ->addField([
                'columnName' => 'reference',
                'fieldName' => 'reference',
                'type' => 'string'
            ])

            ->addField([
                'columnName' => 'coupon',
                'fieldName' => 'voucher',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'type_inscription',
                'fieldName' => 'ticketTypeId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'montant',
                'fieldName' => 'amount',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'informations_reglement',
                'fieldName' => 'paymentInfo',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'civilite',
                'fieldName' => 'civility',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'lastname',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'prenom',
                'fieldName' => 'firstname',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'telephone',
                'fieldName' => 'phoneNumber',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'citer_societe',
                'fieldName' => 'companyCitation',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'newsletter_afup',
                'fieldName' => 'newsletter',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'commentaires',
                'fieldName' => 'comments',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'etat',
                'fieldName' => 'status',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'facturation',
                'fieldName' => 'invoiceStatus',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'id_forum',
                'fieldName' => 'forumId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'mobilite_reduite',
                'fieldName' => 'pmr',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'mail_partenaire',
                'fieldName' => 'optin',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'presence_day1',
                'fieldName' => 'day1Checkin',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
            ->addField([
                'columnName' => 'presence_day2',
                'fieldName' => 'day2Checkin',
                'type' => 'bool',
                'serializer' => Boolean::class
            ])
        ;

        return $metadata;
    }
}
