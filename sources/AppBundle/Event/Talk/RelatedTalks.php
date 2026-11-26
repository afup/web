<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Speaker\SpeakerIdentity;

/**
 * Liens vers d'autres talks affichés sur la page d'un talk : ses voisins dans le planning de l'édition,
 * puis quelques talks de l'édition et de chacun de ses speakers.
 *
 * Chaque section est courte et renvoie à la page complète (édition, speaker) ;
 * un même talk n'est lié qu'une fois sur la page.
 */
final readonly class RelatedTalks
{
    public const int LIMIT = 3;

    /**
     * @param list<PublishedTalk> $sameEvent
     * @param list<array{identity: SpeakerIdentity, talks: list<array{talk: PublishedTalk, event: PublishedEvent}>}> $bySpeaker
     */
    public function __construct(
        public ?PublishedTalk $previous,
        public ?PublishedTalk $next,
        public array $sameEvent,
        public array $bySpeaker,
    ) {}

    /**
     * @param list<EventTalk> $eventTalks talks publiés de l'édition, dans l'ordre du planning
     * @param list<array{identity: SpeakerIdentity, talks: list<array{talk: PublishedTalk, event: PublishedEvent}>}> $speakersTalks talks publiés de chaque speaker, du plus récent au plus ancien
     */
    public static function build(int $talkId, array $eventTalks, array $speakersTalks): self
    {
        $talks = array_map(static fn(EventTalk $eventTalk): PublishedTalk => $eventTalk->talk, $eventTalks);
        $position = array_find_key($talks, static fn(PublishedTalk $talk): bool => $talk->id === $talkId);

        if ($position === null) {
            $previous = $next = null;
            $others = $talks;
        } else {
            $previous = $talks[$position - 1] ?? null;
            $next = $talks[$position + 1] ?? null;
            // On part du talk qui suit le suivant et on boucle sur le début du planning : chaque talk
            // de l'édition reçoit ainsi des liens, pas seulement les premiers du planning.
            $others = [...array_slice($talks, $position + 2), ...array_slice($talks, 0, max(0, $position - 1))];
        }

        // Tri stable : les vidéos d'abord, l'ordre du planning ensuite.
        usort($others, static fn(PublishedTalk $a, PublishedTalk $b): int => $b->hasYoutubeId() <=> $a->hasYoutubeId());
        $sameEvent = array_slice($others, 0, self::LIMIT);

        $linked = [$talkId => true];
        foreach ([$previous, $next, ...$sameEvent] as $talk) {
            if (null !== $talk) {
                $linked[$talk->id] = true;
            }
        }

        $bySpeaker = [];
        foreach ($speakersTalks as $speakerTalks) {
            $selected = [];
            foreach ($speakerTalks['talks'] as $candidate) {
                if (count($selected) === self::LIMIT) {
                    break;
                }
                if (isset($linked[$candidate['talk']->id])) {
                    continue;
                }
                $linked[$candidate['talk']->id] = true;
                $selected[] = $candidate;
            }

            if ($selected !== []) {
                $bySpeaker[] = ['identity' => $speakerTalks['identity'], 'talks' => $selected];
            }
        }

        return new self($previous, $next, $sameEvent, $bySpeaker);
    }
}
