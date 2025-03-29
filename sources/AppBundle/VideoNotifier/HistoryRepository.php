<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Talk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoryEntry>
 */
final class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryEntry::class);
    }

    public function insert(HistoryEntry $history): void
    {
        $this->getEntityManager()->persist($history);
        $this->getEntityManager()->flush();
    }

    /**
     * @param array<Talk> $talks
     * @return array<int, int>
     */
    public function getNumberOfStatusesPerTalk(array $talks): array
    {
        $rows = ($qb = $this->createQueryBuilder('h'))
            ->select('h.talkId', 'COUNT(h.id) AS quantity')
            ->where(
                $qb->expr()->in('h.talkId', array_map(fn (Talk $talk) => $talk->getId(), $talks))
            )
            ->groupBy('h.talkId')
            ->getQuery()
            ->execute();

        $map = [];

        foreach ($rows as $row) {
            $map[$row['talkId']] = $row['quantity'];
        }

        return $map;
    }
}
