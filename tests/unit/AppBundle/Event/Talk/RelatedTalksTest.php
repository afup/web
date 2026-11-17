<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Talk\RelatedTalks;
use PHPUnit\Framework\TestCase;

final class RelatedTalksTest extends TestCase
{
    public function testPreviousAndNextFollowThePlanning(): void
    {
        $talks = $this->talks([1 => false, 2 => false, 3 => false]);

        $related = RelatedTalks::build($talks[1], array_values($talks), [], []);

        self::assertSame($talks[0], $related->previous);
        self::assertSame($talks[2], $related->next);
    }

    public function testFirstAndLastTalksHaveNoPreviousOrNext(): void
    {
        $talks = $this->talks([1 => false, 2 => false]);

        self::assertNull(RelatedTalks::build($talks[0], $talks, [], [])->previous);
        self::assertNull(RelatedTalks::build($talks[1], $talks, [], [])->next);
    }

    public function testSameEventStartsAfterTheTalkAndPutsVideosFirst(): void
    {
        // Planning : 1, 2, 3 (courant), 4, 5 ; seuls 2 et 5 ont une vidéo.
        $talks = $this->talks([1 => false, 2 => true, 3 => false, 4 => false, 5 => true]);

        $related = RelatedTalks::build($talks[2], $talks, [], []);

        self::assertSame([5, 2, 4, 1], $this->ids($related->sameEvent));
    }

    public function testSameEventIsLimitedToSixTalks(): void
    {
        $talks = $this->talks(array_fill(1, 9, false));

        $related = RelatedTalks::build($talks[0], $talks, [], []);

        self::assertSame([2, 3, 4, 5, 6, 7], $this->ids($related->sameEvent));
    }

    public function testUnpublishedTalkHasNoNeighboursButStillLinksToItsEvent(): void
    {
        $talks = $this->talks([1 => false, 2 => true]);
        $unpublished = new Talk()->setId(99)->setTitle('Pas encore publié');

        $related = RelatedTalks::build($unpublished, $talks, [], []);

        self::assertNull($related->previous);
        self::assertNull($related->next);
        self::assertSame([2, 1], $this->ids($related->sameEvent));
    }

    public function testTalksAreGroupedBySpeaker(): void
    {
        $alice = $this->speaker(1, 'Alice', 'Martin', 42);
        $bob = $this->speaker(2, 'Bob', 'Durand', null);

        // Mêmes personnes, autres éditions : Alice retrouvée par son compte GitHub
        // malgré un nom différent, Bob par son nom malgré la casse et les espaces.
        $aliceElsewhere = $this->speaker(10, 'Alicia', 'Martin-Dupont', 42);
        $bobElsewhere = $this->speaker(11, ' bob', 'DURAND ', null);
        $homonym = $this->speaker(12, 'Alice', 'Martinez', 7);

        $event = new Event();
        $talkByAlice = new Talk()->setId(20)->setTitle('Talk Alice');
        $talkByBoth = new Talk()->setId(21)->setTitle('Talk à deux');
        $talkByHomonym = new Talk()->setId(22)->setTitle('Talk homonyme');

        $current = new Talk()->setId(1)->setTitle('Talk courant');
        $related = RelatedTalks::build($current, [$current], [$alice, $bob], [
            ['talk' => $talkByAlice, 'event' => $event, 'speakers' => [$aliceElsewhere]],
            ['talk' => $talkByBoth, 'event' => $event, 'speakers' => [$aliceElsewhere, $bobElsewhere]],
            ['talk' => $talkByHomonym, 'event' => $event, 'speakers' => [$homonym]],
        ]);

        self::assertCount(2, $related->bySpeaker);
        self::assertSame($alice, $related->bySpeaker[0]['speaker']);
        self::assertSame([$talkByAlice, $talkByBoth], array_column($related->bySpeaker[0]['talks'], 'talk'));
        self::assertSame($bob, $related->bySpeaker[1]['speaker']);
        self::assertSame([$talkByBoth], array_column($related->bySpeaker[1]['talks'], 'talk'));
        self::assertSame($event, $related->bySpeaker[1]['talks'][0]['event']);
    }

    public function testSpeakerWithoutOtherTalksIsOmitted(): void
    {
        $current = new Talk()->setId(1)->setTitle('Talk courant');

        $related = RelatedTalks::build($current, [$current], [$this->speaker(1, 'Alice', 'Martin', null)], []);

        self::assertSame([], $related->bySpeaker);
    }

    /**
     * @param array<int, bool> $hasVideoById
     *
     * @return list<Talk>
     */
    private function talks(array $hasVideoById): array
    {
        $talks = [];
        foreach ($hasVideoById as $id => $hasVideo) {
            $talks[] = new Talk()
                ->setId($id)
                ->setTitle('Talk ' . $id)
                ->setYoutubeId($hasVideo ? 'video' . $id : null);
        }

        return $talks;
    }

    private function speaker(int $id, string $firstname, string $lastname, ?int $githubUser): Speaker
    {
        return new Speaker()
            ->setId($id)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setUser($githubUser);
    }

    /**
     * @param list<Talk> $talks
     *
     * @return list<int|null>
     */
    private function ids(array $talks): array
    {
        return array_map(fn(Talk $talk): ?int => $talk->getId(), $talks);
    }
}
