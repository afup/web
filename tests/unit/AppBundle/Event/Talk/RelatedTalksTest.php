<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Talk;

use AppBundle\Event\Speaker\SpeakerIdentity;
use AppBundle\Event\Talk\EventTalk;
use AppBundle\Event\Talk\PublishedEvent;
use AppBundle\Event\Talk\PublishedTalk;
use AppBundle\Event\Talk\RelatedTalks;
use PHPUnit\Framework\TestCase;

final class RelatedTalksTest extends TestCase
{
    public function testPreviousAndNextFollowThePlanning(): void
    {
        $related = RelatedTalks::build(2, $this->eventTalks([1 => false, 2 => false, 3 => false]), []);

        self::assertSame(1, $related->previous?->id);
        self::assertSame(3, $related->next?->id);
    }

    public function testFirstAndLastTalksHaveNoPreviousOrNext(): void
    {
        $eventTalks = $this->eventTalks([1 => false, 2 => false]);

        self::assertNull(RelatedTalks::build(1, $eventTalks, [])->previous);
        self::assertNull(RelatedTalks::build(2, $eventTalks, [])->next);
    }

    public function testSameEventSkipsNeighboursStartsAfterTheTalkAndPutsVideosFirst(): void
    {
        // Planning : 1 à 7, 4 courant ; 3 et 5 sont ses voisins. Seuls 1 et 7 ont une vidéo.
        $related = RelatedTalks::build(4, $this->eventTalks([1 => true, 2 => false, 3 => false, 4 => false, 5 => false, 6 => false, 7 => true]), []);

        self::assertSame([7, 1, 6], $this->ids($related->sameEvent));
    }

    public function testSameEventIsLimitedToThreeTalks(): void
    {
        $related = RelatedTalks::build(1, $this->eventTalks(array_fill(1, 9, false)), []);

        self::assertSame([3, 4, 5], $this->ids($related->sameEvent));
    }

    public function testUnpublishedTalkHasNoNeighboursButStillLinksToItsEvent(): void
    {
        $related = RelatedTalks::build(99, $this->eventTalks([1 => false, 2 => true]), []);

        self::assertNull($related->previous);
        self::assertNull($related->next);
        self::assertSame([2, 1], $this->ids($related->sameEvent));
    }

    public function testSpeakerTalksAreLimitedAndNeverRepeatALinkedTalk(): void
    {
        $alice = $this->identity('alice-martin');
        $bob = $this->identity('bob-durand');
        $event = new PublishedEvent(2, 'Forum PHP 2020', 'forum2020');

        // 2 est le talk suivant, déjà lié ; 20 est commun à Alice et Bob.
        $related = RelatedTalks::build(1, $this->eventTalks([1 => false, 2 => false]), [
            ['identity' => $alice, 'talks' => [
                ['talk' => $this->talk(1), 'event' => $event],
                ['talk' => $this->talk(2), 'event' => $event],
                ['talk' => $this->talk(20), 'event' => $event],
                ['talk' => $this->talk(21), 'event' => $event],
                ['talk' => $this->talk(22), 'event' => $event],
                ['talk' => $this->talk(23), 'event' => $event],
            ]],
            ['identity' => $bob, 'talks' => [
                ['talk' => $this->talk(20), 'event' => $event],
                ['talk' => $this->talk(30), 'event' => $event],
            ]],
        ]);

        self::assertCount(2, $related->bySpeaker);
        self::assertSame($alice, $related->bySpeaker[0]['identity']);
        self::assertSame([20, 21, 22], $this->ids(array_column($related->bySpeaker[0]['talks'], 'talk')));
        self::assertSame($bob, $related->bySpeaker[1]['identity']);
        self::assertSame([30], $this->ids(array_column($related->bySpeaker[1]['talks'], 'talk')));
        self::assertSame($event, $related->bySpeaker[1]['talks'][0]['event']);
    }

    public function testSpeakerWithoutOtherTalksIsOmitted(): void
    {
        $related = RelatedTalks::build(1, $this->eventTalks([1 => false]), [
            ['identity' => $this->identity('alice-martin'), 'talks' => [['talk' => $this->talk(1), 'event' => new PublishedEvent(2, 'Forum PHP 2020', 'forum2020')]]],
        ]);

        self::assertSame([], $related->bySpeaker);
    }

    /**
     * @param array<int, bool> $hasVideoById
     *
     * @return list<EventTalk>
     */
    private function eventTalks(array $hasVideoById): array
    {
        $eventTalks = [];
        foreach ($hasVideoById as $id => $hasVideo) {
            $eventTalks[] = new EventTalk($this->talk($id, $hasVideo), new \DateTimeImmutable('2020-10-01 09:00'), []);
        }

        return $eventTalks;
    }

    private function talk(int $id, bool $hasVideo = false): PublishedTalk
    {
        return new PublishedTalk($id, 'Talk ' . $id, $hasVideo ? 'video' . $id : null);
    }

    private function identity(string $slug): SpeakerIdentity
    {
        return new SpeakerIdentity($slug, 'Prénom', 'Nom', [1], new \DateTimeImmutable('2020-10-01'));
    }

    /**
     * @param list<PublishedTalk> $talks
     *
     * @return list<int>
     */
    private function ids(array $talks): array
    {
        return array_map(static fn(PublishedTalk $talk): int => $talk->id, $talks);
    }
}
