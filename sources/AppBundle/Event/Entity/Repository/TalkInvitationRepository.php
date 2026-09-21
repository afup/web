<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\TalkInvitation;
use AppBundle\Event\Enum\TalkInvitationState;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<TalkInvitation>
 */
final class TalkInvitationRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TalkInvitation::class);
    }

    /**
     * @return list<TalkInvitation>
     */
    public function getPendingInvitationsByTalkId(int $talkId): array
    {
        ($queryBuilder = $this->createQueryBuilder('i'))
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('i.talkId', ':talkId'),
                $queryBuilder->expr()->eq('i.state', ':state'),
            ))
            ->setParameter('talkId', $talkId)
            ->setParameter('state', TalkInvitationState::Pending);

        return $queryBuilder->getQuery()->getResult();
    }
}
