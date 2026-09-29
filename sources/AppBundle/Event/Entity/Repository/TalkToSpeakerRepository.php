<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\TalkToSpeaker;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * @extends EntityRepository<TalkToSpeaker>
 */
final class TalkToSpeakerRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TalkToSpeaker::class);
    }

    public function getNumberOfSpeakers(Event|int $event, ?\DateTime $since = null): int
    {
        if ($event instanceof Event) {
            $event = $event->getId() ?? 0;
        }

        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(DISTINCT tts.conferencier_id)')
            ->from('afup_conferenciers_sessions', 'tts')
            ->innerJoin('tts', 'afup_sessions', 's', 'tts.session_id = s.session_id')
            ->where('s.id_forum = :event')
            ->setParameter('event', $event);

        if ($since instanceof \DateTime) {
            $queryBuilder->andWhere('s.date_soumission >= :since')
                ->setParameter('since', $since->format('Y-m-d'));
        }

        $count = $queryBuilder->executeQuery()->fetchOne();
        Assert::numeric($count);

        return (int) $count;
    }

    /**
     * @param Speaker[] $speakers
     */
    public function replaceSpeakers(Talk $talk, array $speakers): void
    {
        $talkId = $talk->getId();
        Assert::notNull($talkId);

        $targetSpeakerIds = [];
        foreach ($speakers as $speaker) {
            $speakerId = $speaker->getId();
            Assert::notNull($speakerId);
            $targetSpeakerIds[$speakerId] = $speakerId;
        }

        $entityManager = $this->getEntityManager();
        $existingSpeakerIds = [];
        foreach ($this->findBy(['talkId' => $talkId]) as $talkToSpeaker) {
            $existingSpeakerIds[$talkToSpeaker->speakerId] = $talkToSpeaker->speakerId;
            if (!isset($targetSpeakerIds[$talkToSpeaker->speakerId])) {
                $entityManager->remove($talkToSpeaker);
            }
        }

        foreach (array_diff_key($targetSpeakerIds, $existingSpeakerIds) as $speakerId) {
            $entityManager->persist($this->createTalkToSpeaker($talkId, $speakerId));
        }

        $entityManager->flush();
    }

    public function addSpeakerToTalk(Talk $talk, Speaker $speaker): void
    {
        $talkId = $talk->getId();
        $speakerId = $speaker->getId();
        Assert::notNull($talkId);
        Assert::notNull($speakerId);

        if ($this->find(['talkId' => $talkId, 'speakerId' => $speakerId]) !== null) {
            return;
        }

        $entityManager = $this->getEntityManager();
        $entityManager->persist($this->createTalkToSpeaker($talkId, $speakerId));
        $entityManager->flush();
    }

    private function createTalkToSpeaker(int $talkId, int $speakerId): TalkToSpeaker
    {
        $talkToSpeaker = new TalkToSpeaker();
        $talkToSpeaker->talkId = $talkId;
        $talkToSpeaker->speakerId = $speakerId;

        return $talkToSpeaker;
    }
}
