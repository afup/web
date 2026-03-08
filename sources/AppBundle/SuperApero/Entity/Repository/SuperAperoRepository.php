<?php

declare(strict_types=1);

namespace AppBundle\SuperApero\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\SuperApero\Entity\SuperApero;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Clock\ClockInterface;

/**
 * @extends EntityRepository<SuperApero>
 */
final class SuperAperoRepository extends EntityRepository
{
    private ClockInterface $clock;

    public function __construct(ManagerRegistry $registry, ClockInterface $clock)
    {
        parent::__construct($registry, SuperApero::class);

        $this->clock = $clock;
    }

    /**
     * @return array<SuperApero>
     */
    public function getAllSortedByYear(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.date', 'desc')
            ->getQuery()
            ->execute();
    }

    public function findOneByYear(int $year): ?SuperApero
    {
        return $this->createQueryBuilder('s')
            ->where('s.date >= :yearStart')
            ->andWhere('s.date <= :yearEnd')
            ->setParameter('yearStart', new DateTimeImmutable($year . '-01-01'))
            ->setParameter('yearEnd', new DateTimeImmutable($year . '-12-31'))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActive(): ?SuperApero
    {
        $now = $this->clock->now();

        return $this->createQueryBuilder('s')
            // Le prochain Super Apéro doit être aujourd'hui ou dans le futur
            ->where('s.date >= :now')
            // Et doit avoir lieu dans l'année courante
            ->andWhere('s.date >= :yearStart')
            ->andWhere('s.date <= :yearEnd')
            // Et doit avoir au moins un meetup associé
            ->innerJoin('s.meetups', 'm')
            ->setParameter('now', new DateTimeImmutable($now->format('Y-m-d')))
            ->setParameter('yearStart', new DateTimeImmutable($now->format('Y') . '-01-01'))
            ->setParameter('yearEnd', new DateTimeImmutable($now->format('Y') . '-12-31'))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
