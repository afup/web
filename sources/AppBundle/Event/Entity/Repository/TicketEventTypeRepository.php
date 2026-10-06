<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<TicketEventType>
 */
final class TicketEventTypeRepository extends EntityRepository
{
    public const int REMOVE_PAST_TICKETS = 1;
    public const int REMOVE_FUTURE_TICKETS = 2;
    public const int ACTUAL_TICKETS_ONLY = 3; // Combinaison de REMOVE_PAST_TICKETS et REMOVE_FUTURE_TICKETS

    public function __construct(
        ManagerRegistry $registry,
        private readonly TicketTypeRepository $ticketTypeRepository,
    ) {
        parent::__construct($registry, TicketEventType::class);
    }

    /**
     * Retourne les tarifs d'un évènement, éventuellement filtrés :
     * - publicOnly : uniquement les types de tarifs publics ;
     * - datesFilter : masque de bits des constantes REMOVE_PAST_TICKETS / REMOVE_FUTURE_TICKETS.
     *
     * @return list<TicketEventType>
     */
    public function getTicketsByEvent(Event $event, bool $publicOnly = true, ?int $datesFilter = null): array
    {
        $queryBuilder = ($this->getEntityManager()->getConnection())->createQueryBuilder()
            ->select('tarif_event.id_tarif', 'tarif_event.id_event')
            ->from('afup_forum_tarif_event', 'tarif_event')
            ->innerJoin('tarif_event', 'afup_forum_tarif', 'tarif', 'tarif.id = tarif_event.id_tarif')
            ->where('tarif_event.id_event = :event')
            ->setParameter('event', $event->getId());

        if (($datesFilter & self::REMOVE_PAST_TICKETS) !== 0) {
            $queryBuilder->andWhere('tarif_event.date_end > NOW()');
        }
        if (($datesFilter & self::REMOVE_FUTURE_TICKETS) !== 0) {
            $queryBuilder->andWhere('tarif_event.date_start < NOW()');
        }
        if ($publicOnly === true) {
            $queryBuilder->andWhere('tarif.public = 1');
        }

        $queryBuilder
            ->orderBy('tarif_event.date_start')
            ->addOrderBy('tarif_event.date_end')
            ->addOrderBy('tarif_event.price')
            ->addOrderBy('tarif.members_only', 'DESC');

        $ticketTypes = [];
        foreach ($this->ticketTypeRepository->getAll() as $ticketType) {
            $ticketTypes[$ticketType->getId()] = $ticketType;
        }

        $ticketEventTypes = [];
        foreach ($queryBuilder->executeQuery()->fetchAllAssociative() as $row) {
            if (!isset($row['id_tarif'], $row['id_event'])
                || !is_numeric($row['id_tarif'])
                || !is_numeric($row['id_event'])
            ) {
                continue;
            }
            $ticketEventType = $this->find([
                'ticketTypeId' => (int) $row['id_tarif'],
                'eventId' => (int) $row['id_event'],
            ]);
            if ($ticketEventType === null) {
                continue;
            }
            $ticketEventType->ticketType = $ticketTypes[$ticketEventType->ticketTypeId] ?? null;
            $ticketEventTypes[] = $ticketEventType;
        }

        return $ticketEventTypes;
    }

    /**
     * Indique si l'évènement propose au moins un tarif réservé aux membres.
     */
    public function doesEventHasRestrictedToMembersTickets(Event $event, bool $publicOnly = true, ?int $datesFilter = null): bool
    {
        foreach ($this->getTicketsByEvent($event, $publicOnly, $datesFilter) as $ticketEventType) {
            if ($ticketEventType->ticketType?->getIsRestrictedToMembers() === true) {
                return true;
            }
        }

        return false;
    }
}
