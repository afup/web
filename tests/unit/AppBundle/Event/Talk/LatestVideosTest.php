<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Talk\LatestVideo;
use AppBundle\Event\Talk\LatestVideos;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Clock\MockClock;

final class LatestVideosTest extends TestCase
{
    public function testMapsTalksToVideosAndCachesTheResult(): void
    {
        $now = new \DateTimeImmutable('2026-11-17 12:00:00');

        $talk = new Talk();
        $talk->setId(42);
        $talk->setTitle('Les fibers expliquées');
        $talk->setYoutubeId('abc123');

        $event = new Event();
        $event->setTitle('Forum PHP 2026');

        $talkRepository = $this->createMock(TalkRepository::class);
        $talkRepository
            ->expects(self::once())
            ->method('getLatestPublishedWithVideo')
            ->with($now, LatestVideos::LIMIT)
            ->willReturn([['talk' => $talk, 'event' => $event]]);

        $latestVideos = new LatestVideos($talkRepository, new MockClock($now), new ArrayAdapter());

        $expected = [new LatestVideo(42, 'les-fibers-expliquees', 'Les fibers expliquées', 'abc123', 'Forum PHP 2026')];
        self::assertEquals($expected, $latestVideos->get());
        // Le second appel est servi par le cache : le repository n'est appelé qu'une fois.
        self::assertEquals($expected, $latestVideos->get());
    }

    public function testThumbnailUrl(): void
    {
        $video = new LatestVideo(42, 'slug', 'Titre', 'abc123', 'Forum PHP 2026');

        self::assertSame('https://img.youtube.com/vi/abc123/hqdefault.jpg', $video->getThumbnailUrl());
    }
}
