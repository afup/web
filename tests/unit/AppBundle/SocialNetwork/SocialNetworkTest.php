<?php

declare(strict_types=1);

namespace AppBundle\Tests\SocialNetwork;

use AppBundle\Event\Model\Speaker;
use AppBundle\SocialNetwork\SocialNetwork;
use AppBundle\SocialNetwork\StatusId;
use AppBundle\VideoNotifier\HistoryEntry;
use PHPUnit\Framework\TestCase;

class SocialNetworkTest extends TestCase
{
    /**
     * @dataProvider speakerHandleDataProvider
     */
    public function testGetSpeakerHandle(SocialNetwork $socialNetwork, Speaker $speaker, string $expectedHandle): void
    {
        $actualHandle = $socialNetwork->getSpeakerHandle($speaker);

        self::assertEquals($expectedHandle, $actualHandle);
    }

    public function speakerHandleDataProvider(): \Generator
    {
        yield 'bluesky without @ prefix' => [
            SocialNetwork::Bluesky,
            (new Speaker())->setBluesky('foo.bar'),
            '@foo.bar',
        ];

        yield 'bluesky with @ prefix' => [
            SocialNetwork::Bluesky,
            (new Speaker())->setBluesky('@foo.bar'),
            '@foo.bar',
        ];

        yield 'mastodon without @ prefix' => [
            SocialNetwork::Mastodon,
            (new Speaker())->setMastodon('foo.bar'),
            '@foo.bar',
        ];

        yield 'mastodon with @ prefix' => [
            SocialNetwork::Mastodon,
            (new Speaker())->setMastodon('@foo.bar'),
            '@foo.bar',
        ];
    }

    public function testSetHistoryStatusIdBluesky(): void
    {
        $entry = new HistoryEntry(123);

        SocialNetwork::Bluesky->setStatusId($entry, new StatusId('abcd'));

        self::assertEquals('abcd', $entry->getStatusIdBluesky());
        self::assertNull($entry->getStatusIdMastodon());
    }

    public function testSetHistoryStatusIdMastodon(): void
    {
        $entry = new HistoryEntry(123);

        SocialNetwork::Mastodon->setStatusId($entry, new StatusId('abcd'));

        self::assertEquals('abcd', $entry->getStatusIdMastodon());
        self::assertNull($entry->getStatusIdBluesky());
    }
}
