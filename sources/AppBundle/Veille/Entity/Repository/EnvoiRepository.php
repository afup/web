<?php

declare(strict_types=1);

namespace AppBundle\Veille\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Veille\Entity\Envoi;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Envoi>
 */
class EnvoiRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Envoi::class);
    }

    /**
     * @return list<Envoi>
     */
    public function getAllOrderedByDateDesc(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Sending date and raw JSON content of the techletters sent to Mailchimp, most recent first.
     * Only scalars are selected: no entity hydration, the caller decodes the content it needs.
     *
     * @return list<array{dateEnvoi: \DateTimeInterface, contenu: ?string}>
     */
    public function getSentContentsOrderedByDateDesc(): array
    {
        /** @var list<array{dateEnvoi: \DateTimeInterface, contenu: ?string}> $rows */
        $rows = $this->createQueryBuilder('e')
            ->select('e.dateEnvoi', 'e.contenu')
            ->where('e.envoyeMailchimp = true')
            ->orderBy('e.dateEnvoi', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return $rows;
    }

    /**
     * @return list<Envoi>
     */
    public function getAllPreviouslySent(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.envoyeMailchimp = true')
            ->andWhere('e.dateEnvoi < :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
