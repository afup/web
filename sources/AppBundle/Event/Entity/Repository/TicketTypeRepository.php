<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Entity\TicketType;
use AppBundle\Event\Model\TicketAggregate;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<TicketType>
 */
final class TicketTypeRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketType::class);
    }

    /**
     * @return list<TicketType>
     */
    public function findAllOrderedById(): array
    {
        /** @var list<TicketType> $ticketTypes */
        $ticketTypes = $this->findBy([], ['id' => 'ASC']);

        return $ticketTypes;
    }

    /**
     * @param list<int> $ids
     *
     * @return array<int, TicketType>
     */
    public function findIndexedByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $ticketTypes = [];
        foreach ($this->findBy(['id' => array_values(array_unique($ids))]) as $ticketType) {
            $ticketTypes[$ticketType->id] = $ticketType;
        }

        return $ticketTypes;
    }

    /**
     * Renseigne le type de tarif (champ non mappé) de chacun des tarifs d'évènement.
     *
     * @param iterable<TicketEventType> $ticketEventTypes
     */
    public function attachTo(iterable $ticketEventTypes): void
    {
        $ticketEventTypes = is_array($ticketEventTypes) ? $ticketEventTypes : iterator_to_array($ticketEventTypes, false);

        $ticketTypes = $this->findIndexedByIds(array_values(array_map(
            static fn(TicketEventType $ticketEventType): int => $ticketEventType->ticketTypeId,
            $ticketEventTypes,
        )));

        foreach ($ticketEventTypes as $ticketEventType) {
            $ticketEventType->ticketType = $ticketTypes[$ticketEventType->ticketTypeId] ?? null;
        }
    }

    /**
     * Complète les agrégats d'inscriptions produits par InscriptionRepository avec
     * le type de tarif de chaque inscription, rechargé via Doctrine.
     *
     * @param array<TicketAggregate> $ticketAggregates
     *
     * @return list<TicketAggregate>
     */
    public function enrichTicketAggregates(array $ticketAggregates): array
    {
        $ticketTypes = $this->findIndexedByIds(array_values(array_map(
            static fn(TicketAggregate $ticketAggregate): int => $ticketAggregate->ticket->ticketTypeId,
            $ticketAggregates,
        )));

        $enriched = [];
        foreach ($ticketAggregates as $ticketAggregate) {
            $enriched[] = new TicketAggregate(
                $ticketAggregate->ticket,
                $ticketTypes[$ticketAggregate->ticket->ticketTypeId] ?? null,
                $ticketAggregate->invoice,
                $ticketAggregate->lastSubscription,
            );
        }

        return $enriched;
    }
}
