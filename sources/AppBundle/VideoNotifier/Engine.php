<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\SocialNetwork\Transport;
use CCMBenchmark\Ting\Repository\CollectionInterface;

final class Engine
{
    private const VALID_TALK_TYPES = [
        Talk::TYPE_FULL_LONG,
        Talk::TYPE_FULL_SHORT,
    ];

    private PlanningRepository $planningRepository;
    private TalkRepository $talkRepository;
    private EventRepository $eventRepository;
    private SpeakerRepository $speakerRepository;
    private HistoryRepository $historyRepository;

    /** @var iterable<Transport> */
    private iterable $transports;

    /**
     * @param iterable<Transport> $transports
     */
    public function __construct(
        iterable $transports,
        PlanningRepository $planningRepository,
        TalkRepository $talkRepository,
        EventRepository $eventRepository,
        SpeakerRepository $speakerRepository,
        HistoryRepository $historyRepository
    ) {
        $this->transports = $transports;
        $this->planningRepository = $planningRepository;
        $this->talkRepository = $talkRepository;
        $this->eventRepository = $eventRepository;
        $this->speakerRepository = $speakerRepository;
        $this->historyRepository = $historyRepository;
    }

    public function run(): void
    {
        $talk = $this->pickRandomTalk();

        if ($talk === null) {
            return; // todo log ?
        }

        $speakers = $this->speakerRepository->getSpeakersByTalk($talk);

        $historyEntry = new HistoryEntry($talk->getId());

        foreach ($this->transports as $transport) {
            $statusGenerator = new StatusGenerator($transport->socialNetwork());

            $status = $statusGenerator->generate($talk, iterator_to_array($speakers));

            $statusId = $transport->send($status);

            if ($statusId !== null) {
                $transport->socialNetwork()->setStatusId($historyEntry, $statusId);
            }
        }

        $this->historyRepository->insert($historyEntry);
    }

    private function pickRandomTalk(): ?Talk
    {
        // todo add a way to pick an event (like for twitter)
        $minimumEventDate = new \DateTime('-20 years'); // todo

        /** @var CollectionInterface<Planning>&iterable<Planning> $plannings */
        $plannings = $this->planningRepository->getAll();

        $talks = [];

        /** @var Planning $planning */
        foreach ($plannings as $planning) {
            /** @var Talk|null $talk */
            $talk = $this->talkRepository->get($planning->getTalkId());

            if (null === $talk
                || !$talk->isDisplayedOnHistory()
                || !$talk->hasYoutubeId()
                || !in_array($talk->getType(), self::VALID_TALK_TYPES, true)
            ) {
                continue;
            }

            /** @var Event|null $event */
            $event = $this->eventRepository->get($planning->getEventId());

            if (null === $event || $event->startsBefore($minimumEventDate)) {
                continue;
            }

            $talks[] = $talk;
        }

        $talksWithLessPosts = $this->findLessPostedTalks($talks);

        if ($talksWithLessPosts === []) {
            return null;
        }

        return $talksWithLessPosts[array_rand($talksWithLessPosts)];
    }

    /**
     * @param array<Talk> $talks
     * @return array<Talk>
     */
    private function findLessPostedTalks(array $talks): array
    {
        $quantities = $this->historyRepository->getNumberOfStatusesPerTalk($talks);
        $maxCount = 0;
        $minCount = PHP_INT_MAX;

        foreach ($talks as $talk) {
            $count = $quantities[$talk->getId()] ?? 0;

            $maxCount = max($maxCount, $count);
            $minCount = max($minCount, $count);
        }

        if ($minCount === $maxCount) {
            return $talks;
        }

        return array_filter($talks, fn (Talk $talk) => ($quantities[$talk->getId()] ?? 0) !== $maxCount);
    }
}
