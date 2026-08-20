<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Antennes\Antenne;
use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\Meetup;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Clock\ClockAwareTrait;

/**
 * @extends EntityRepository<Meetup>
 */
final class MeetupRepository extends EntityRepository
{
    use ClockAwareTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meetup::class);
    }

    public function findNextForAntenne(Antenne $antenne): ?Meetup
    {
        /** @var Meetup|null $meetup */
        $meetup = ($qb = $this->createQueryBuilder('m'))
            ->where($qb->expr()->eq('m.codeAntenne', ':codeAntenne'))
            ->setParameter('codeAntenne', $antenne->code)
            ->andWhere($qb->expr()->gt('m.date', ':after'))
            ->setParameter('after', $this->now()->modify('midnight'))
            ->orderBy('m.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $meetup;
    }

    /**
     * @return array<Meetup>
     */
    public function findAllForAntenne(Antenne $antenne): array
    {
        return ($qb = $this->createQueryBuilder('m'))
            ->where($qb->expr()->eq('m.codeAntenne', ':codeAntenne'))
            ->setParameter('codeAntenne', $antenne->code)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Meetup>
     */
    public function findNextEvents(int $quantity): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.date', 'DESC')
            ->setMaxResults($quantity)
            ->getQuery()
            ->getResult();
    }
}
