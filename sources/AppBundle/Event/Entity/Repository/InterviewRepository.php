<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\Interview;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Interview>
 */
final class InterviewRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interview::class);
    }

    public function findOneBySpeakerId(int $speakerId): ?Interview
    {
        /** @var Interview|null $interview */
        $interview = ($qb = $this->createQueryBuilder('i'))
            ->innerJoin('i.speakers', 's')
            ->where($qb->expr()->eq('s.id', ':id'))
            ->setParameter('id', $speakerId)
            ->getQuery()
            ->getOneOrNullResult();

        return $interview;
    }

    /**
     * @param list<int> $speakerIds
     *
     * @return array<int, Interview>
     */
    public function findIndexedBySpeakerIds(array $speakerIds): array
    {
        $indexed = [];
        foreach ($this->findBySpeakerIds($speakerIds) as $interview) {
            foreach ($interview->getSpeakerIds() as $speakerId) {
                if (in_array($speakerId, $speakerIds, true)) {
                    $indexed[$speakerId] = $interview;
                }
            }
        }

        return $indexed;
    }

    /**
     * @param list<int> $speakerIds
     *
     * @return list<Interview>
     */
    public function findBySpeakerIds(array $speakerIds): array
    {
        if ($speakerIds === []) {
            return [];
        }

        return ($qb = $this->createQueryBuilder('i'))
            ->innerJoin('i.speakers', 's')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $speakerIds)
            ->getQuery()
            ->getResult();
    }
}
