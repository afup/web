<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Model;

use AppBundle\Event\Model\Speaker;
use PHPUnit\Framework\TestCase;

final class SpeakerTest extends TestCase
{
    /**
     * @dataProvider mastodonDataProvider
     */
    public function testMastodon(string $mastodon, string $expectedUsername, string $expectedUrl): void
    {
        $speaker = new Speaker();
        $speaker->setMastodon($mastodon);

        self::assertEquals($expectedUsername, $speaker->getUsernameMastodon());
        self::assertEquals($expectedUrl, $speaker->getUrlMastodon());
    }

    public function mastodonDataProvider(): array
    {
        return [
            ['', '', ''],
            ['https://phpc.social/@username', 'username', 'https://phpc.social/@username'],
            ['https://mastodon.social/@username', 'username', 'https://mastodon.social/@username'],
            ['https://@username@mastodon.social', 'username', 'https://mastodon.social/@username'],
            ['http://@username@mastodon.social', 'username', 'https://mastodon.social/@username'],
        ];
    }

    /**
     * @dataProvider twitterDataProvider
     */
    public function testTwitter(string $twitter, string $expectedUsername, string $expectedUrl): void
    {
        $speaker = new Speaker();
        $speaker->setTwitter($twitter);

        self::assertEquals($expectedUsername, $speaker->getUsernameTwitter());
        self::assertEquals($expectedUrl, $speaker->getUrlTwitter());
    }

    public function twitterDataProvider(): array
    {
        return [
            ['', '', ''],
            ['https://twitter.com/username', 'username', 'https://x.com/username'],
            ['https://x.com/username', 'username', 'https://x.com/username'],
            ['http://twitter.com/username', 'username', 'https://x.com/username'],
        ];
    }
}
