<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Talk;

use AppBundle\Event\Talk\LatestVideo;
use AppBundle\Event\Talk\LatestVideoRepository;
use AppBundle\Event\Talk\LatestVideos;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Clock\MockClock;

final class LatestVideosTest extends TestCase
{
    public function testMapsRowsToVideosAndCachesTheResult(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn([[
                'session_id' => '42',
                'titre' => 'Les fibers expliquées',
                'youtube_id' => 'abc123',
                'event_title' => 'Forum PHP 2026',
            ]]);

        $latestVideos = new LatestVideos(
            new LatestVideoRepository($connection),
            new MockClock(new \DateTimeImmutable('2026-11-17 12:00:00')),
            new ArrayAdapter(),
        );

        $expected = [new LatestVideo(42, 'les-fibers-expliquees', 'Les fibers expliquées', 'abc123', 'Forum PHP 2026')];
        self::assertEquals($expected, $latestVideos->get());
        // Le second appel est servi par le cache : la base n'est interrogée qu'une fois.
        self::assertEquals($expected, $latestVideos->get());
    }
}
