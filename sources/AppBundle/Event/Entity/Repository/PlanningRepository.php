<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Doctrine\Type\UnixTimestampType;
use AppBundle\Event\Entity\Planning;
use AppBundle\Event\Entity\Room;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TalkAggregate;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * @extends EntityRepository<Planning>
 */
final class PlanningRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planning::class);
    }

    public function getByTalk(Talk $talk): ?Planning
    {
        $talkId = $talk->getId();
        Assert::notNull($talkId);

        return $this->findOneBy(['talkId' => $talkId]);
    }

    /**
     * @return list<Planning>
     */
    public function findNonKeynotesBetween(\DateTimeInterface $since, \DateTimeInterface $until): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.isKeynote = :keynote')
            ->andWhere('p.start >= :since')
            ->andWhere('p.start < :until')
            ->setParameter('keynote', false)
            ->setParameter('since', $since, UnixTimestampType::NAME)
            ->setParameter('until', $until, UnixTimestampType::NAME)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère le premier planning (par date de début) de chacune des sessions demandées.
     *
     * @param list<int> $talkIds
     *
     * @return array<int, Planning>
     */
    public function findIndexedByTalkIds(array $talkIds): array
    {
        if ($talkIds === []) {
            return [];
        }

        $plannings = [];
        foreach ($this->findBy(['talkId' => $talkIds], ['start' => 'ASC', 'roomId' => 'ASC']) as $planning) {
            $talkId = $planning->talkId;
            if ($talkId === null) {
                continue;
            }
            $plannings[$talkId] ??= $planning;
        }

        return $plannings;
    }

    /**
     * Complète les agrégats session/conférenciers produits par TalkRepository (Ting)
     * avec les plannings et les salles correspondants, rechargés via Doctrine.
     *
     * @param array<TalkAggregate> $talkAggregates
     *
     * @return array<TalkAggregate>
     */
    public function enrichTalkAggregates(array $talkAggregates): array
    {
        $talkIds = [];
        foreach ($talkAggregates as $talkAggregate) {
            $talkId = $talkAggregate->talk->getId();
            if ($talkId !== null) {
                $talkIds[] = $talkId;
            }
        }

        $plannings = $this->findIndexedByTalkIds($talkIds);
        $rooms = $this->findRoomsOf($plannings);

        $enriched = [];
        foreach ($talkAggregates as $talkAggregate) {
            $talkId = $talkAggregate->talk->getId();
            $planning = $talkId !== null ? ($plannings[$talkId] ?? null) : null;
            $enriched[] = new TalkAggregate(
                $talkAggregate->talk,
                $talkAggregate->speakers,
                $planning?->roomId !== null ? ($rooms[$planning->roomId] ?? null) : null,
                $planning,
                $talkAggregate->vote,
            );
        }

        return $enriched;
    }

    /**
     * @param array<Planning> $plannings
     *
     * @return array<int, Room>
     */
    public function findRoomsOf(array $plannings): array
    {
        $roomIds = [];
        foreach ($plannings as $planning) {
            if ($planning->roomId !== null) {
                $roomIds[] = $planning->roomId;
            }
        }

        return $this->getEntityManager()->getRepository(Room::class)->findIndexedByIds($roomIds);
    }
}
