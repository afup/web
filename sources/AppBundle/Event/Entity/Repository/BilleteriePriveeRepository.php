<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Model\Ticket;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<BilleteriePrivee>
 */
final class BilleteriePriveeRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BilleteriePrivee::class);
    }

    /**
     * @return list<BilleteriePrivee>
     */
    public function findByEvent(int $eventId): array
    {
        /** @var list<BilleteriePrivee> $billeteries */
        $billeteries = $this->findBy(['eventId' => $eventId], ['id' => 'DESC']);

        return $billeteries;
    }

    /**
     * @param list<string> $tokens
     * @return list<BilleteriePrivee>
     */
    public function findByTokens(array $tokens): array
    {
        if ($tokens === []) {
            return [];
        }

        /** @var list<BilleteriePrivee> $billeteries */
        $billeteries = $this->createQueryBuilder('billeterie_privee')
            ->where('billeterie_privee.token IN (:tokens)')
            ->setParameter('tokens', $tokens)
            ->getQuery()
            ->getResult()
        ;

        return $billeteries;
    }

    public function findOneByToken(?string $token): ?BilleteriePrivee
    {
        if (!is_string($token) || $token === '') {
            return null;
        }

        /** @var BilleteriePrivee|null $billeteriePrivee */
        $billeteriePrivee = $this->findOneBy(['token' => $token]);

        return $billeteriePrivee;
    }

    /**
     * Nombre de places déjà prises pour cette billeterie privée
     * (les inscriptions annulées ne consomment pas de place).
     */
    public function countPlacesPrisesParToken(string $token): int
    {
        $count = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(inscription.id)')
            ->from(Inscription::class, 'inscription')
            ->where('inscription.specialPriceToken = :token')
            ->andWhere('inscription.etat <> :etatAnnule')
            ->setParameter('token', $token)
            ->setParameter('etatAnnule', Ticket::STATUS_CANCELLED)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $count;
    }
}
