<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Entity;

use AppBundle\Event\Entity\Speaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SpeakerTest extends TestCase
{
    #[DataProvider('mastodonDataProvider')]
    public function testMastodon(string $mastodon, string $expectedUsername, ?string $expectedUrl): void
    {
        $speaker = new Speaker();
        $speaker->mastodon = $mastodon;

        self::assertEquals($expectedUsername, $speaker->getUsernameMastodon());
        self::assertEquals($expectedUrl, $speaker->getUrlMastodon());
    }

    public static function mastodonDataProvider(): array
    {
        return [
            ['', '', null],
            ['https://phpc.social/@username', 'username', 'https://phpc.social/@username'],
            ['https://mastodon.social/@username', 'username', 'https://mastodon.social/@username'],
            ['https://@username@mastodon.social', 'username', 'https://mastodon.social/@username'],
            ['http://@username@mastodon.social', 'username', 'https://mastodon.social/@username'],
        ];
    }

    #[DataProvider('twitterDataProvider')]
    public function testTwitter(string $twitter, string $expectedUsername, string $expectedUrl): void
    {
        $speaker = new Speaker();
        $speaker->twitter = $twitter;

        self::assertEquals($expectedUsername, $speaker->getUsernameTwitter());
        self::assertEquals($expectedUrl, $speaker->getUrlTwitter());
    }

    public static function twitterDataProvider(): array
    {
        return [
            ['', '', ''],
            ['https://twitter.com/username', 'username', 'https://x.com/username'],
            ['https://x.com/username', 'username', 'https://x.com/username'],
            ['http://twitter.com/username', 'username', 'https://x.com/username'],
        ];
    }

    #[DataProvider('linkedinDataProvider')]
    public function testLinkedin(?string $linkedin, ?string $expectedUsername, ?string $expectedUrl): void
    {
        $speaker = new Speaker();
        $speaker->linkedin = $linkedin;

        self::assertSame($expectedUsername, $speaker->getUsernameLinkedin());
        self::assertSame($expectedUrl, $speaker->getUrlLinkedin());
    }

    public static function linkedinDataProvider(): array
    {
        return [
            [null, null, null],
            ['  ', null, null],
            ['username/', 'username', 'https://www.linkedin.com/in/username'],
            ['https://www.linkedin.com/in/username/', 'username', 'https://www.linkedin.com/in/username'],
            ['https://example.com/username', null, null],
        ];
    }

    #[DataProvider('blueskyDataProvider')]
    public function testBlueskyIsNormalized(?string $bluesky, ?string $expectedHandle, ?string $expectedUrl): void
    {
        $speaker = new Speaker();
        $speaker->bluesky = $bluesky;

        self::assertSame($expectedHandle, $speaker->bluesky);
        self::assertSame($expectedUrl, $speaker->getUrlBluesky());
    }

    public static function blueskyDataProvider(): array
    {
        return [
            [null, null, null],
            ['foo.bsky.social', 'foo.bsky.social', 'https://bsky.app/profile/foo.bsky.social'],
            ['@foo.bsky.social', 'foo.bsky.social', 'https://bsky.app/profile/foo.bsky.social'],
            ['https://bsky.app/profile/foo.bsky.social', 'foo.bsky.social', 'https://bsky.app/profile/foo.bsky.social'],
        ];
    }

    public function testHotelNightsAreUnknownUntilFilled(): void
    {
        $speaker = new Speaker();

        self::assertNull($speaker->getHotelNightsArray());
        self::assertNull($speaker->hasHotelNightBefore());
        self::assertNull($speaker->hasHotelNightBetween());
        self::assertNull($speaker->hasHotelNightAfter());
        self::assertNull($speaker->hasNoHotelNight());
    }

    public function testNoHotelNight(): void
    {
        $speaker = new Speaker();
        $speaker->setHotelNightsArray([]);

        self::assertSame('', $speaker->hotelNights);
        self::assertSame([], $speaker->getHotelNightsArray());
        self::assertTrue($speaker->hasNoHotelNight());
        self::assertFalse($speaker->hasHotelNightBefore());
    }

    public function testHotelNights(): void
    {
        $speaker = new Speaker();
        $speaker->setHotelNightsArray([Speaker::NIGHT_BEFORE, Speaker::NIGHT_AFTER]);

        self::assertSame('before,after', $speaker->hotelNights);
        self::assertSame([Speaker::NIGHT_BEFORE, Speaker::NIGHT_AFTER], $speaker->getHotelNightsArray());
        self::assertTrue($speaker->hasHotelNightBefore());
        self::assertFalse($speaker->hasHotelNightBetween());
        self::assertTrue($speaker->hasHotelNightAfter());
        self::assertFalse($speaker->hasNoHotelNight());
    }

    #[DataProvider('labelDataProvider')]
    public function testLabel(?string $firstname, ?string $lastname, string $expectedLabel): void
    {
        $speaker = new Speaker();
        $speaker->firstname = $firstname;
        $speaker->lastname = $lastname;

        self::assertSame($expectedLabel, $speaker->label);
    }

    public static function labelDataProvider(): array
    {
        return [
            ['Hélène', 'Schapira', 'Hélène SCHAPIRA'],
            ['Hélène', null, 'Hélène '],
            ['Hélène', '', 'Hélène '],
            [null, 'Schapira', ' SCHAPIRA'],
            [null, null, ' '],
        ];
    }
}
