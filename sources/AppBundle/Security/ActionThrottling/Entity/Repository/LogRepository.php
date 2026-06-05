<?php

declare(strict_types=1);

namespace AppBundle\Security\ActionThrottling\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Security\ActionThrottling\Entity\Log;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Log>
 */
class LogRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * @return array{ip: int, object: int}
     */
    public function getApplicableLogs(?string $ip, ?int $objectId, \DateInterval $interval): array
    {
        if ($ip === null && $objectId === null) {
            throw new \RuntimeException('I need at least an ip or an object Id to get logs');
        }

        $dateCreation = (new \DateTime())->sub($interval);

        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.ip) AS ip, COUNT(l.idObjet) AS object')
            ->where('l.dateCreation > :dateCreation')
            ->setParameter('dateCreation', $dateCreation)
        ;

        $conditions = [];
        if ($ip !== null) {
            $conditions[] = $qb->expr()->eq('l.ip', ':ip');
            $qb->setParameter('ip', ip2long($ip));
        }
        if ($objectId !== null) {
            $conditions[] = $qb->expr()->eq('l.idObjet', ':idObjet');
            $qb->setParameter('idObjet', $objectId);
        }
        $qb->andWhere($qb->expr()->orX(...$conditions));

        return $qb->getQuery()->getSingleResult();
    }

    public function removeLogs(string $action, string $ip): void
    {
        $this->createQueryBuilder('l')
            ->delete()
            ->where('l.action = :action')
            ->andWhere('l.ip = :ip')
            ->setParameter('action', $action)
            ->setParameter('ip', ip2long($ip))
            ->getQuery()
            ->execute()
        ;
    }

    public function clearOldLogs(\DateInterval $delay): void
    {
        $date = (new \DateTime())->sub($delay);

        $this->createQueryBuilder('l')
            ->delete()
            ->where('l.dateCreation < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute()
        ;
    }
}
