<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity\Repository;

use AppBundle\AssembleeGenerale\Entity\AssembleeGenerale;
use AppBundle\Doctrine\EntityRepository;
use AppBundle\Doctrine\Type\UnixTimestampType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<AssembleeGenerale>
 */
class AssembleeGeneraleRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssembleeGenerale::class);
    }

    public function getLatestDate(): ?\DateTimeImmutable
    {
        $ts = $this->getEntityManager()->getConnection()->fetchOne(
            'SELECT MAX(date) FROM afup_assemblee_generale',
        );

        return $ts ? new \DateTimeImmutable('@' . $ts) : null;
    }

    public function hasPlanned(?\DateTimeInterface $currentDate = null): bool
    {
        $currentDate ??= new \DateTime();
        $latestDate = $this->getLatestDate();

        return null !== $latestDate
            && $latestDate->getTimestamp() > strtotime('-1 day', $currentDate->getTimestamp());
    }

    public function findOneByDate(\DateTimeInterface $date): ?AssembleeGenerale
    {
        return $this->createQueryBuilder('ag')
            ->where('ag.date = :date')
            ->setParameter('date', \DateTime::createFromFormat('U', $date->format('U')), UnixTimestampType::NAME)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function upsert(\DateTimeInterface $date, string $description): void
    {
        $assemblee = $this->findOneByDate($date) ?? new AssembleeGenerale();
        $assemblee->date = \DateTime::createFromFormat('U', $date->format('U'));
        $assemblee->description = $description;
        $this->save($assemblee);
    }
}
