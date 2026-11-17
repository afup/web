<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Speaker;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Speaker\PersonJsonLd;
use AppBundle\Event\Speaker\SpeakerIdentity;
use AppBundle\Event\Speaker\SpeakerProfile;
use AppBundle\Event\Speaker\SpeakerTalk;
use PHPUnit\Framework\TestCase;

final class PersonJsonLdTest extends TestCase
{
    public function testGetData(): void
    {
        $profile = self::profile([
            'twitter' => 'https://x.com/ubermuda',
            'github' => 'https://github.com/ubermuda',
        ]);

        self::assertSame([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => 'Geoffrey Bachelet',
            'url' => 'https://afup.org/talks/speakers/geoffrey-bachelet',
            'image' => 'https://afup.org/uploads/speakers/1/thumbnails/1968.jpg',
            'sameAs' => ['https://x.com/ubermuda', 'https://github.com/ubermuda'],
        ], new PersonJsonLd()->getData($profile, 'https://afup.org/talks/speakers/geoffrey-bachelet', 'https://afup.org/uploads/speakers/1/thumbnails/1968.jpg'));
    }

    public function testWithoutPhotoNorSocialLinks(): void
    {
        self::assertSame([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => 'Geoffrey Bachelet',
            'url' => 'https://afup.org/talks/speakers/geoffrey-bachelet',
        ], new PersonJsonLd()->getData(self::profile([]), 'https://afup.org/talks/speakers/geoffrey-bachelet', null));
    }

    public function testEncodedJsonCannotCloseTheScriptTag(): void
    {
        $profile = new SpeakerProfile(
            new SpeakerIdentity('x', '</script><script>alert(1)</script>', 'Doe', [1], new \DateTimeImmutable()),
            new Speaker(),
            null,
            null,
            [],
            [new SpeakerTalk(new Speaker(), new Talk(), new Event(), new \DateTimeImmutable())],
        );

        $json = json_encode(new PersonJsonLd()->getData($profile, 'https://afup.org/talks/speakers/x', null), PersonJsonLd::JSON_FLAGS);

        self::assertIsString($json);
        self::assertStringNotContainsString('<', $json);
        self::assertSame('</script><script>alert(1)</script> Doe', json_decode($json, true)['name']);
    }

    /**
     * @param array<'twitter'|'mastodon'|'bluesky'|'linkedin'|'github', string> $socialLinks
     */
    private static function profile(array $socialLinks): SpeakerProfile
    {
        $speaker = new Speaker()->setId(1)->setFirstname('Geoffrey')->setLastname('Bachelet');

        return new SpeakerProfile(
            new SpeakerIdentity('geoffrey-bachelet', 'Geoffrey', 'Bachelet', [1], new \DateTimeImmutable()),
            $speaker,
            null,
            null,
            $socialLinks,
            [new SpeakerTalk($speaker, new Talk()->setId(1), new Event(), new \DateTimeImmutable())],
        );
    }
}
