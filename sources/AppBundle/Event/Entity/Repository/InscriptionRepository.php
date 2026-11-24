<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Entity\TicketType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\TicketAggregate;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use AppBundle\Event\Enum\InscriptionEtat;

/**
 * @extends EntityRepository<Inscription>
 */
final class InscriptionRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    /**
     * @return list<Inscription>
     */
    public function getByReference(string $reference): array
    {
        /** @var list<Inscription> $inscriptions */
        $inscriptions = $this->findBy(['reference' => $reference], ['id' => 'ASC']);

        return $inscriptions;
    }

    /**
     * Inscriptions d'une commande avec leur tarif d'évènement (sans son type de tarif).
     * Les inscriptions sans tarif d'évènement correspondant sont écartées.
     *
     * @return list<Inscription>
     */
    public function getByReferenceWithTicketEventType(string $reference): array
    {
        $ticketEventTypes = [];
        $inscriptions = [];

        foreach ($this->getByReference($reference) as $inscription) {
            $key = $inscription->ticketTypeId . '-' . $inscription->forumId;

            if (!array_key_exists($key, $ticketEventTypes)) {
                $ticketEventTypes[$key] = $this->getEntityManager()->find(TicketEventType::class, [
                    'ticketTypeId' => $inscription->ticketTypeId,
                    'eventId' => $inscription->forumId,
                ]);
            }

            if (!$ticketEventTypes[$key] instanceof TicketEventType) {
                continue;
            }

            $inscription->setTicketEventType($ticketEventTypes[$key]);
            $inscriptions[] = $inscription;
        }

        return $inscriptions;
    }

    /**
     * @return list<Inscription>
     */
    public function getByEmptyQrCodes(): array
    {
        /** @var list<Inscription> $inscriptions */
        $inscriptions = $this->findBy(['qrCode' => null], ['id' => 'ASC']);

        return $inscriptions;
    }

    public function getTotalOfSoldTicketsByMember(int $memberType, int $memberId, int $eventId): int
    {
        $total = $this->getEntityManager()->getConnection()->fetchOne(
            'SELECT COUNT(inscriptions.id) AS total
            FROM afup_inscription_forum inscriptions
            WHERE inscriptions.id_member = :member
            AND inscriptions.member_type = :type
            AND inscriptions.id_forum = :forum
            AND inscriptions.etat = :state',
            [
                'member' => $memberId,
                'type' => $memberType,
                'forum' => $eventId,
                'state' => InscriptionEtat::Reglee->value,
            ],
        );

        return is_numeric($total) ? (int) $total : 0;
    }

    /**
     * @param iterable<Event> $events
     *
     * @return list<array{nom: string, prenom: string, email: string}>
     */
    public function getRegistrationsForEventsWithNewsletterAllowed(iterable $events): array
    {
        $eventIds = [];
        foreach ($events as $event) {
            $eventIds[] = (int) $event->getId();
        }

        if ($eventIds === []) {
            return [];
        }

        /** @var list<array{nom: string, prenom: string, email: string}> $registrations */
        $registrations = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT afup_inscription_forum.nom,
                    afup_inscription_forum.prenom,
                    afup_inscription_forum.email
             FROM afup_inscription_forum
             WHERE afup_inscription_forum.id_forum IN (:ids)
               AND afup_inscription_forum.newsletter_afup = 1
             GROUP BY afup_inscription_forum.nom, afup_inscription_forum.prenom, afup_inscription_forum.email
             ORDER BY afup_inscription_forum.nom, afup_inscription_forum.prenom, afup_inscription_forum.email',
            ['ids' => $eventIds],
            ['ids' => ArrayParameterType::INTEGER],
        );

        return $registrations;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getWithTicketTypeByReference(string $reference): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT aif.*, aft.pretty_name
            FROM afup_inscription_forum aif
            LEFT JOIN afup_forum_tarif aft ON aft.id = aif.type_inscription
            WHERE aif.reference = :reference',
            ['reference' => $reference],
        );
    }

    public function getPublicSoldTickets(Event $event): int
    {
        return $this->getPublicSoldTicketsByDayOfType($event);
    }

    public function getPublicSoldTicketsByDay(?string $day, Event $event): int
    {
        return $this->getPublicSoldTicketsByDayOfType($event, $day);
    }

    public function getPublicSoldTicketsOfType(Event $event, ?TicketType $ticketType = null): int
    {
        return $this->getPublicSoldTicketsByDayOfType($event, null, $ticketType);
    }

    public function getPublicSoldTicketsByDayOfType(Event $event, ?string $day = null, ?TicketType $ticketType = null): int
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
            'state_cancelled' => InscriptionEtat::Annulee->value,
        ];

        if (null !== $day) {
            $sql .= '  AND FIND_IN_SET(:day, aft.day) > 0 ';
            $params['day'] = $day;
        }

        if ($ticketType instanceof TicketType) {
            $sql .= ' AND aif.type_inscription = :ticket_type_id';
            $params['ticket_type_id'] = $ticketType->id;
        }

        $soldTickets = $this->getEntityManager()->getConnection()->fetchOne($sql, $params);

        return is_numeric($soldTickets) ? (int) $soldTickets : 0;
    }

    /**
     * Inscriptions d'un évènement avec la date de fin de la dernière cotisation de la personne.
     * Le type de tarif et la facture sont à compléter avec TicketTypeRepository::enrichTicketAggregates()
     * et InvoiceRepository::enrichTicketAggregates().
     *
     * @return list<TicketAggregate>
     */
    public function getByEventWithAll(Event $event, ?string $search, ?string $sortKey, ?string $sortDirection): array
    {
        $sql = 'SELECT ticket.id,
        CASE WHEN ticket.id_member IS NOT NULL
        THEN (SELECT MAX(ac.date_fin) AS last_subscription
              FROM afup_cotisations ac
              WHERE ac.type_personne = ticket.member_type AND ac.id_personne = ticket.id_member)
        ELSE (SELECT MAX(ac.date_fin) AS last_subscription
              FROM afup_personnes_physiques app
              LEFT JOIN afup_personnes_morales apm ON apm.id = app.id_personne_morale
              LEFT JOIN afup_cotisations ac ON ac.type_personne = IF(apm.id IS NULL, 0, 1) AND ac.id_personne = IFNULL(apm.id, app.id)
              WHERE app.email = ticket.email
              GROUP BY app.id)
        END AS last_subscription
        FROM afup_inscription_forum AS ticket
        LEFT JOIN afup_facturation_forum aff ON ticket.reference = aff.reference
        LEFT JOIN afup_forum_tarif_event afte ON afte.id_tarif = ticket.type_inscription AND afte.id_event = ticket.id_forum
        LEFT JOIN afup_forum_tarif aft ON aft.id = afte.id_tarif
        WHERE %s
        ORDER BY %s';

        $andWhere = 'ticket.id_forum = :id_forum';
        $params = ['id_forum' => $event->getId()];

        if ($search) {
            $andWhere .= ' AND (LOWER(CONCAT(ticket.nom, ticket.prenom)) LIKE :q
            OR LOWER(CONCAT(ticket.prenom, ticket.nom)) LIKE :q
            OR LOWER(aff.societe) LIKE :q)';
            $params['q'] = '%' . strtolower($search) . '%';
        }

        $column = match ($sortKey) {
            'label' => 'ticket.nom',
            'company' => 'aff.societe',
            'ticketType' => 'aft.id',
            'ticketStatus' => 'ticket.etat',
            default => 'ticket.date',
        };
        $direction = strtolower((string) $sortDirection) === 'asc' ? 'ASC' : 'DESC';

        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            sprintf($sql, $andWhere, $column . ' ' . $direction),
            $params,
        );

        // Date de fin de la dernière cotisation par inscription, dans l'ordre du tri
        $lastSubscriptions = [];
        foreach ($rows as $row) {
            if (is_numeric($row['id'])) {
                $lastSubscriptions[(int) $row['id']] = is_numeric($row['last_subscription']) ? (int) $row['last_subscription'] : null;
            }
        }

        if ($lastSubscriptions === []) {
            return [];
        }

        $inscriptions = [];
        foreach ($this->findBy(['id' => array_keys($lastSubscriptions)]) as $inscription) {
            $inscriptions[$inscription->id] = $inscription;
        }

        $aggregates = [];
        foreach ($lastSubscriptions as $id => $lastSubscription) {
            if (!array_key_exists($id, $inscriptions)) {
                continue;
            }

            $aggregates[] = new TicketAggregate(
                $inscriptions[$id],
                null,
                null,
                $lastSubscription ? new \DateTimeImmutable('@' . $lastSubscription) : null,
            );
        }

        return $aggregates;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function searchAllPastEvents(string $search): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT insc.*, forum.titre AS forum_titre
            FROM afup_inscription_forum AS insc
            LEFT JOIN afup_forum AS forum ON insc.id_forum = forum.id
            WHERE
              insc.reference LIKE :like
              OR insc.informations_reglement LIKE :like
              OR insc.commentaires LIKE :like
              OR insc.nom LIKE :like
              OR insc.prenom LIKE :like
              OR insc.email LIKE :like',
            ['like' => '%' . $search . '%'],
        );
    }
}
