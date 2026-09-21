<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\TicketType;
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
}
