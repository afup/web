<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;

/**
 * Liens vers d'autres talks affichés sur la page d'un talk.
 */
final readonly class RelatedTalks
{
    public const int SAME_EVENT_LIMIT = 6;

    /**
     * @param list<Talk> $sameEvent
     * @param list<array{speaker: Speaker, talks: list<array{talk: Talk, event: Event}>}> $bySpeaker
     */
    public function __construct(
        public ?Talk $previous,
        public ?Talk $next,
        public array $sameEvent,
        public array $bySpeaker,
    ) {}

    /**
     * @param list<Talk> $eventTalks talks publiés de l'édition, dans l'ordre du planning
     * @param list<Speaker> $speakers speakers du talk
     * @param list<array{talk: Talk, event: Event, speakers: list<Speaker>}> $speakersTalks talks publiés des speakers, du plus récent au plus ancien
     */
    public static function build(Talk $talk, array $eventTalks, array $speakers, array $speakersTalks): self
    {
        $position = array_find_key($eventTalks, fn(Talk $eventTalk): bool => $eventTalk->getId() === $talk->getId());

        if ($position === null) {
            $previous = $next = null;
            $others = $eventTalks;
        } else {
            $previous = $eventTalks[$position - 1] ?? null;
            $next = $eventTalks[$position + 1] ?? null;
            // On part du talk suivant et on boucle sur le début du planning : chaque talk
            // de l'édition reçoit ainsi des liens, pas seulement les premiers du planning.
            $others = [...array_slice($eventTalks, $position + 1), ...array_slice($eventTalks, 0, $position)];
        }

        // Tri stable : les vidéos d'abord, l'ordre du planning ensuite.
        usort($others, fn(Talk $a, Talk $b): int => $b->hasYoutubeId() <=> $a->hasYoutubeId());

        $bySpeaker = [];
        foreach ($speakers as $speaker) {
            $talks = [];
            foreach ($speakersTalks as $speakerTalk) {
                foreach ($speakerTalk['speakers'] as $candidate) {
                    if (self::isSamePerson($speaker, $candidate)) {
                        $talks[] = ['talk' => $speakerTalk['talk'], 'event' => $speakerTalk['event']];
                        break;
                    }
                }
            }

            if ($talks !== []) {
                $bySpeaker[] = ['speaker' => $speaker, 'talks' => $talks];
            }
        }

        return new self($previous, $next, array_slice($others, 0, self::SAME_EVENT_LIMIT), $bySpeaker);
    }

    /**
     * Même règle que TalkRepository::getPublishedBySpeakers() : même compte GitHub,
     * ou même prénom et même nom.
     */
    private static function isSamePerson(Speaker $speaker, Speaker $candidate): bool
    {
        if ($speaker->getUser() && $speaker->getUser() === $candidate->getUser()) {
            return true;
        }

        return self::normalize($speaker->getFirstname()) === self::normalize($candidate->getFirstname())
            && self::normalize($speaker->getLastname()) === self::normalize($candidate->getLastname());
    }

    private static function normalize(?string $name): string
    {
        return mb_strtolower(trim((string) $name));
    }
}
