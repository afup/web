<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\SocialNetwork\Transport;
use Exception;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

final readonly class Engine
{
    private const VALID_TALK_TYPES = [
        Talk::TYPE_FULL_LONG,
        Talk::TYPE_FULL_SHORT,
    ];

    /**
     * @param iterable<Transport> $transports
     */
    public function __construct(
        private iterable $transports,
        private PlanningRepository $planningRepository,
        private TalkRepository $talkRepository,
        private SpeakerRepository $speakerRepository,
        private HistoryRepository $historyRepository,
        private LoggerInterface $logger,
        private ClockInterface $clock,
    ) {
    }

    public function run(): ?HistoryEntry
    {
        $talk = $this->pickRandomTalk();

        if ($talk === null) {
            $this->logger->debug('[video-notifier] no talk to post');
            return null;
        }

        $this->logger->debug('[video-notifier] Talk to post: ' . $talk->getId());

        $speakers = $this->speakerRepository->getSpeakersByTalk($talk);

        $historyEntry = new HistoryEntry($talk->getId());
        $atLeastOneSuccessfulTransport = false;

        foreach ($this->transports as $transport) {
            $statusGenerator = new StatusGenerator($transport->socialNetwork());

            try {
                $status = $statusGenerator->generate($talk, iterator_to_array($speakers));

                $statusId = $transport->send($status);

                if ($statusId !== null) {
                    $transport->socialNetwork()->setStatusId($historyEntry, $statusId);

                    $atLeastOneSuccessfulTransport = true;
                }
            } catch (Exception $e) {
                // On ignore les erreurs pour pouvoir tenter avec un autre transport
                $this->logger->error(sprintf(
                    '[video-notifier] %s error: %s',
                    $transport->socialNetwork()->value,
                    $e->getMessage(),
                ));
            }
        }

        // L'insert n'est fait que si au moins un des transports a fonctionné.
        // Cela permettra au talk de rester possible à poster la fois suivante.
        if ($atLeastOneSuccessfulTransport) {
            $this->historyRepository->insert($historyEntry);

            return $historyEntry;
        }

        return null;
    }

    private function pickRandomTalk(): ?Talk
    {
        $minimumEventDate = new \DateTime('-2 years');

        $plannings = $this->planningRepository->findNonKeynotesBetween($minimumEventDate, $this->clock->now());

        $talkIds = [];

        /** @var Planning $planning */
        foreach ($plannings as $planning) {
            $talkIds[] = $planning->getTalkId();
        }

        if (empty($talkIds)) {
            return null;
        }

        $talks = array_filter(
            iterator_to_array($this->talkRepository->findList($talkIds)),
            fn (Talk $talk): bool => $talk->isDisplayedOnHistory()
                && $talk->hasYoutubeId()
                && in_array($talk->getType(), self::VALID_TALK_TYPES, true)
        );

        if (empty($talks)) {
            return null;
        }

        $talksWithLessPosts = $this->findLessPostedTalks($talks);

        if ($talksWithLessPosts === []) {
            return null;
        }

        return $talksWithLessPosts[array_rand($talksWithLessPosts)];
    }

    /**
     * Cette fonction filtre la liste de tous les talks sélectionnés pour trouver ceux qui ont été les moins postés.
     * Cela permet d'éviter les doublons de posts.
     *
     * @param array<Talk> $talks
     * @return array<Talk>
     */
    private function findLessPostedTalks(array $talks): array
    {
        $quantities = $this->historyRepository->getNumberOfStatusesPerTalk($talks);

        // S'il n'y a aucun historique ou que tous les talks ont la même quantité de posts, on retourne toute la liste.
        if (count($quantities) === 0 || count(array_count_values($quantities)) === 1) {
            return $talks;
        }

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

        return array_filter($talks, fn (Talk $talk): bool => ($quantities[$talk->getId()] ?? 0) !== $maxCount);
    }
}
