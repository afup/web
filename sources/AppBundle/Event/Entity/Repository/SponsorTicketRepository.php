<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\SponsorTicket;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<SponsorTicket>
 */
final class SponsorTicketRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SponsorTicket::class);
    }

    /**
     * @return list<SponsorTicket>
     */
    public function findByEventId(int $eventId): array
    {
        /** @var list<SponsorTicket> $sponsorTickets */
        $sponsorTickets = $this->findBy(['eventId' => $eventId]);

        return $sponsorTickets;
    }
}
