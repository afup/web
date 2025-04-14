<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketType;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Query\QueryException;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<Ticket>
 */
class TicketRepository extends Repository implements MetadataInitializer
{
    /**
     * @param string $reference
     *
     * @return CollectionInterface<Ticket>&iterable<Ticket>
     */
    public function getByReference($reference): CollectionInterface
    {
        return $this->getBy(['reference' => $reference]);
    }

    public function getTotalOfSoldTicketsByMember($userType, $userId, $eventId)
    {
        try {
            return $this->getPreparedQuery(
                'SELECT COUNT(inscriptions.id) AS total
            FROM afup_inscription_forum inscriptions
            WHERE inscriptions.id_member = :member
            AND inscriptions.member_type = :type
            AND inscriptions.id_forum = :forum
            AND inscriptions.etat = :state'
            )->setParams([
                'member' => $userId,
                'type' => $userType,
                'forum' => $eventId,
                'state' => Ticket::STATUS_PAID
            ])->query($this->getCollection(new HydratorArray()))->first()['total'];
        } catch (QueryException $exception) {
            return 0;
        }
    }

    /**
     * @param Event[]|\Traversable $events
     *
     * @return CollectionInterface
     */
    public function getRegistrationsForEventsWithNewsletterAllowed(\Traversable $events)
    {
        $params = [];
        $idsParams = [];
        $cpt = 0;
        foreach ($events as $event) {
            $key = 'event_id' . ++$cpt;
            $params[$key] = $event->getId();
            $idsParams[] = ':' . $key;
        }
        return $this
            ->getPreparedQuery(strtr(
                'SELECT afup_inscription_forum.nom,
                        afup_inscription_forum.prenom,
                        afup_inscription_forum.email
                 FROM afup_inscription_forum
                 WHERE afup_inscription_forum.id_forum IN (%ids%)
                   AND afup_inscription_forum.newsletter_afup = 1
                 GROUP BY afup_inscription_forum.nom, afup_inscription_forum.prenom, afup_inscription_forum.email
                 ORDER BY afup_inscription_forum.nom, afup_inscription_forum.prenom, afup_inscription_forum.email',
                [
                    '%ids%' => implode(',', $idsParams),
                ]
            ))
            ->setParams($params)
            ->query($this->getCollection(new HydratorArray()))
       ;
    }

    public function getByInvoiceWithDetail(Invoice $invoice)
    {
        return $this->getPreparedQuery(
            'SELECT
            inscriptions.id, inscriptions.date, inscriptions.reference, inscriptions.coupon, inscriptions.type_inscription,
            inscriptions.montant, inscriptions.informations_reglement, inscriptions.civilite, inscriptions.nom, inscriptions.prenom,
            inscriptions.email, inscriptions.telephone, inscriptions.citer_societe, inscriptions.newsletter_afup, inscriptions.newsletter_nexen,
            inscriptions.commentaires, inscriptions.etat, inscriptions.facturation, inscriptions.id_forum,
            inscriptions.mail_partenaire, inscriptions.presence_day1, inscriptions.presence_day2,
            tarif_event.id_tarif, tarif_event.id_event, tarif_event.price, tarif_event.date_start, tarif_event.date_end, tarif_event.description,
            tarif.id, tarif.technical_name, tarif.day, tarif.pretty_name, tarif.public, tarif.members_only, tarif.default_price, tarif.active
            FROM afup_inscription_forum inscriptions
            JOIN afup_forum_tarif_event tarif_event ON tarif_event.id_tarif = inscriptions.type_inscription AND tarif_event.id_event = inscriptions.id_forum
            JOIN afup_forum_tarif tarif ON tarif.id = tarif_event.id_tarif
            WHERE inscriptions.reference = :ref
            '
        )->setParams(['ref' => $invoice->getReference()])->query(
            $this->getCollection(
                (new HydratorSingleObject())
                ->mapObjectTo('tarif', 'tarif_event', 'setTicketType')
                ->mapObjectTo('tarif_event', 'inscriptions', 'setTicketEventType')
            )
        );
    }

    public function getPublicSoldTickets(Event $event)
    {
        return $this->getPublicSoldTicketsByDayOfType($event);
    }

    public function getPublicSoldTicketsByDay($day, Event $event)
    {
        return $this->getPublicSoldTicketsByDayOfType($event, $day);
    }

    public function getPublicSoldTicketsByDayOfType(Event $event, $day = null, TicketType $ticketType = null)
    {
        $sql = '
            SELECT COUNT(aif.id) AS sold_tickets
            FROM afup_inscription_forum aif
            JOIN afup_forum_tarif aft ON aft.id = aif.type_inscription
            WHERE aif.id_forum = :event AND aft.public = 1
            AND aif.etat <> :state_cancelled
        ';

        $params = [
            'event' => $event->getId(),
            'state_cancelled' => Ticket::STATUS_CANCELLED,
        ];

        if (null !== $day) {
            $sql .= '  AND FIND_IN_SET(:day, aft.day) > 0 ';
            $params['day'] = $day;
        }

        if ($ticketType instanceof TicketType) {
            $sql .= ' AND aif.type_inscription = :ticket_type_id';
            $params['ticket_type_id'] = $ticketType->getId();
        }

        $tickets = $this->getPreparedQuery($sql)
            ->setParams($params)
            ->query($this->getCollection(new HydratorArray()))
        ;

        if ($tickets === null) {
            return 0;
        }

        return $tickets->first()['sold_tickets'];
    }

    public function getPublicSoldTicketsOfType(Event $event, TicketType $ticketType = null)
    {
        return $this->getPublicSoldTicketsByDayOfType($event, null, $ticketType);
    }

    /**
     * @return CollectionInterface<Ticket>&iterable<Ticket>
     */
    public function getByEvent(Event $event): CollectionInterface
    {
        $sql = 'SELECT afup_inscription_forum.*
        FROM afup_inscription_forum
        WHERE afup_inscription_forum.id_forum = :id_forum
        ORDER BY afup_inscription_forum.date';

        return $this->getPreparedQuery($sql)
            ->setParams(['id_forum' => $event->getId()])
            ->query($this->getCollection(new HydratorSingleObject()))
        ;
    }

    /**
     * @return CollectionInterface<Ticket>&iterable<Ticket>
     */
    public function getByEmptyQrCodes(): CollectionInterface
    {
        $sql = 'SELECT afup_inscription_forum.*
        FROM afup_inscription_forum
        WHERE afup_inscription_forum.qr_code IS NULL';

        return $this->getQuery($sql)
            ->query($this->getCollection(new HydratorSingleObject()))
            ;
    }

    /**
     * @return CollectionInterface
     */
    public function getAllTicketsForExport()
    {
        return
            $this
                ->getPreparedQuery(
                    'SELECT inscriptions.id, inscriptions.date, inscriptions.reference, inscriptions.coupon, inscriptions.type_inscription,
                    inscriptions.montant, inscriptions.informations_reglement, inscriptions.civilite, inscriptions.nom, inscriptions.prenom,
                    inscriptions.email, inscriptions.id_forum
                    FROM afup_inscription_forum inscriptions
                    WHERE inscriptions.etat = :state
                    '
                )
                ->setParams(['state' => Ticket::STATUS_PAID])
                ->query($this->getCollection(new HydratorSingleObject()));
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
                'type' => 'bool',
                'serializer' => Boolean::class
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
                'columnName' => 'id_member',
                'fieldName' => 'memberId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'member_type',
                'fieldName' => 'memberType',
                'type' => 'int'
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
            ->addField([
                'columnName' => 'special_price_token',
                'fieldName' => 'specialPriceToken',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'nearest_office',
                'fieldName' => 'nearestOffice',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'transport_mode',
                'fieldName' => 'transportMode',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'transport_distance',
                'fieldName' => 'transportDistance',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'qr_code',
                'fieldName' => 'qrCode',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
