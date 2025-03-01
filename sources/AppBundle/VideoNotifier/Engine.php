<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TweetRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Tweet;
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
    private TweetRepository $tweetRepository;

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
        TweetRepository $tweetRepository
    ) {
        $this->planningRepository = $planningRepository;
        $this->talkRepository = $talkRepository;
        $this->eventRepository = $eventRepository;
        $this->speakerRepository = $speakerRepository;
        $this->tweetRepository = $tweetRepository;
        $this->transports = $transports;
    }

    public function run(): void
    {
        $talk = $this->pickRandomTalk();

        if ($talk === null) {
            return; // todo log ?
        }

        foreach ($this->transports as $transport) {
            $statusGenerator = new StatusGenerator($transport->socialNetwork());

            $status = $statusGenerator->generate($talk, $this->speakerRepository->getSpeakersByTalk($talk));

            $statusId = $transport->send($status);

            $tweet = new Tweet();
            $tweet->setId($statusId->value);
            $tweet->setTalkId($talk->getId());
            $tweet->setCreatedAt(new \DateTime());
            $tweet->setSocialNetwork($transport->socialNetwork()->getValue());

            $this->tweetRepository->save($tweet);
        }
    }

    public function pickRandomTalk(): ?Talk
    {
        // todo add a way to pick an event (like for twitter)
        $minimumEventDate = new \DateTime('-2 years');

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

            $eventId = $planning->getEventId();

            /** @var Event|null $event */
            $event = $this->eventRepository->get($eventId);

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
        $quantities = $this->tweetRepository->getNumberOfTweetsPerTalk($talks);
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

        return array_map(fn (Talk $talk) => ($quantities[$talk->getId()] ?? 0) !== $maxCount, $talks);
    }
}
