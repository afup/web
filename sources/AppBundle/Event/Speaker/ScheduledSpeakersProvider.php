<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;

/**
 * Les conférenciers d'un événement ayant au moins une session planifiée, avec ces sessions.
 *
 * Les conférenciers sont lus via Doctrine, les sessions via Ting (TalkRepository).
 */
final readonly class ScheduledSpeakersProvider
{
    public function __construct(
        private SpeakerRepository $speakerRepository,
        private TalkRepository $talkRepository,
    ) {}

    /**
     * @return list<array{speaker: Speaker, talks: list<Talk>}> triés par prénom puis nom
     */
    public function getScheduledSpeakers(int $eventId, bool $includeUnpublished = false): array
    {
        $talkIdsBySpeaker = $this->speakerRepository->getScheduledSpeakerTalkIds($eventId, $includeUnpublished);
        if ($talkIdsBySpeaker === []) {
            return [];
        }

        $speakers = $this->speakerRepository->findIndexedByIds(array_keys($talkIdsBySpeaker));

        $talks = [];
        /** @var Talk $talk */
        foreach ($this->talkRepository->findList(array_values(array_unique(array_merge(...array_values($talkIdsBySpeaker))))) as $talk) {
            $talks[(int) $talk->getId()] = $talk;
        }

        $scheduledSpeakers = [];
        foreach ($talkIdsBySpeaker as $speakerId => $talkIds) {
            if (!array_key_exists($speakerId, $speakers)) {
                continue;
            }

            $scheduledSpeakers[] = [
                'speaker' => $speakers[$speakerId],
                'talks' => array_values(array_filter(array_map(static fn(int $talkId): ?Talk => $talks[$talkId] ?? null, $talkIds))),
            ];
        }

        return $scheduledSpeakers;
    }
}
