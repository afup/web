<?php

declare(strict_types=1);

namespace AppBundle\Tests\VideoNotifier;

use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\SocialNetwork\Embed;
use AppBundle\SocialNetwork\SocialNetwork;
use AppBundle\SocialNetwork\Status;
use AppBundle\Tests\TestCase;
use AppBundle\VideoNotifier\StatusGenerator;

class StatusGeneratorTest extends TestCase
{
    public function testThrowsIfNoSpeaker(): void
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Aucun speaker pour le talk');

        $generator = new StatusGenerator(SocialNetwork::Bluesky);

        $generator->generate(new Talk(), []);
    }

    /**
     * @dataProvider textTooLongDataProvider
     */
    public function testTextTooLong(
        SocialNetwork $socialNetwork,
        Talk $talk,
        array $speakers,
        string $expectedExceptionMessage,
    ): void {
        self::expectException(\LengthException::class);
        self::expectExceptionMessage($expectedExceptionMessage);

        $generator = new StatusGenerator($socialNetwork);

        $generator->generate($talk, $speakers);
    }

    public function textTooLongDataProvider(): \Generator
    {
        yield [
            SocialNetwork::Bluesky,
            (new Talk())->setTitle($this->faker()->realText(SocialNetwork::Bluesky->statusMaxLength() * 2)),
            [
                (new Speaker())->setBluesky($this->faker()->domainName()),
            ],
            'Statut généré pour bluesky trop long',
        ];

        yield [
            SocialNetwork::Mastodon,
            (new Talk())->setTitle($this->faker()->realText(SocialNetwork::Mastodon->statusMaxLength() * 2)),
            [
                (new Speaker())->setMastodon($this->faker()->domainName()),
            ],
            'Statut généré pour mastodon trop long',
        ];
    }

    /**
     * @dataProvider validStatusDataProvider
     */
    public function testGenerateValidStatus(
        SocialNetwork $socialNetwork,
        Talk $talk,
        array $speakers,
        Status $expectedStatus,
    ): void {
        $generator = new StatusGenerator($socialNetwork);

        $actualStatus = $generator->generate($talk, $speakers);

        self::assertEquals($expectedStatus, $actualStatus);
    }

    public function validStatusDataProvider(): \Generator
    {
        yield 'mastodon full case' => [
            SocialNetwork::Mastodon,
            (new Talk())
                ->setId(123)
                ->setTitle('Lorem ipsum dolor si amet')
                ->setAbstract('Foo bar')
                ->setYoutubeId('abcd1234'),
            [
                (new Speaker())->setMastodon('foo'),
                (new Speaker())->setFirstname('John')->setLastname('Smith'),
                (new Speaker())->setMastodon('bar'),
                (new Speaker())->setFirstname('Jane')->setLastname('Doe'),
                (new Speaker())->setMastodon('fiz'),
            ],
            new Status(
                "« Lorem ipsum dolor si amet », la conférence de @foo, John SMITH, @bar, Jane DOE et @fiz à revoir sur le site de l'AFUP",
                new Embed(
                    'https://afup.org/talks/123-lorem-ipsum-dolor-si-amet',
                    'Lorem ipsum dolor si amet',
                    'Foo bar',
                    'https://i.ytimg.com/vi_webp/abcd1234/maxresdefault.webp'
                )
            ),
        ];

        yield '1 speaker with username' => [
            SocialNetwork::Bluesky,
            (new Talk())
                ->setId(123)
                ->setTitle('Lorem ipsum dolor si amet')
                ->setAbstract('Foo bar')
                ->setYoutubeId('abcd1234'),
            [
                (new Speaker())->setBluesky('example.bsky.social'),
            ],
            new Status(
                "« Lorem ipsum dolor si amet », la conférence de @example.bsky.social à revoir sur le site de l'AFUP",
                new Embed(
                    'https://afup.org/talks/123-lorem-ipsum-dolor-si-amet',
                    'Lorem ipsum dolor si amet',
                    'Foo bar',
                    'https://i.ytimg.com/vi_webp/abcd1234/maxresdefault.webp'
                )
            ),
        ];

        yield '1 speaker without username' => [
            SocialNetwork::Bluesky,
            (new Talk())
                ->setId(123)
                ->setTitle('Lorem ipsum dolor si amet')
                ->setAbstract('Foo bar')
                ->setYoutubeId('abcd1234'),
            [
                (new Speaker())->setFirstname('Jane')->setLastname('Doe'),
            ],
            new Status(
                "« Lorem ipsum dolor si amet », la conférence de Jane DOE à revoir sur le site de l'AFUP",
                new Embed(
                    'https://afup.org/talks/123-lorem-ipsum-dolor-si-amet',
                    'Lorem ipsum dolor si amet',
                    'Foo bar',
                    'https://i.ytimg.com/vi_webp/abcd1234/maxresdefault.webp'
                )
            ),
        ];

        yield '2 speakers' => [
            SocialNetwork::Bluesky,
            (new Talk())
                ->setId(123)
                ->setTitle('Lorem ipsum dolor si amet')
                ->setAbstract('Foo bar')
                ->setYoutubeId('abcd1234'),
            [
                (new Speaker())->setBluesky('foo'),
                (new Speaker())->setBluesky('bar'),
            ],
            new Status(
                "« Lorem ipsum dolor si amet », la conférence de @foo et @bar à revoir sur le site de l'AFUP",
                new Embed(
                    'https://afup.org/talks/123-lorem-ipsum-dolor-si-amet',
                    'Lorem ipsum dolor si amet',
                    'Foo bar',
                    'https://i.ytimg.com/vi_webp/abcd1234/maxresdefault.webp'
                )
            ),
        ];

        yield '3+ speakers' => [
            SocialNetwork::Bluesky,
            (new Talk())
                ->setId(123)
                ->setTitle('Lorem ipsum dolor si amet')
                ->setAbstract('Foo bar')
                ->setYoutubeId('abcd1234'),
            [
                (new Speaker())->setBluesky('foo'),
                (new Speaker())->setBluesky('bar'),
                (new Speaker())->setBluesky('fiz'),
            ],
            new Status(
                "« Lorem ipsum dolor si amet », la conférence de @foo, @bar et @fiz à revoir sur le site de l'AFUP",
                new Embed(
                    'https://afup.org/talks/123-lorem-ipsum-dolor-si-amet',
                    'Lorem ipsum dolor si amet',
                    'Foo bar',
                    'https://i.ytimg.com/vi_webp/abcd1234/maxresdefault.webp'
                )
            ),
        ];

        yield 'title a bit too long 1' => [
            SocialNetwork::Bluesky,
            (new Talk())
                ->setId(123)
                ->setTitle('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab accusantium architecto assumenda consequuntur corporis dolorem doloremque eaque magnam mollitia necessitatibus neque nesciunt, obcaecati odio odit quam, repellat repellendus.')
                ->setAbstract('Foo bar')
                ->setYoutubeId('abcd1234'),
            [
                (new Speaker())->setBluesky('example.bsky.social'),
            ],
            new Status(
                "« Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab accusantium architecto assumenda consequuntur corporis dolorem doloremque eaque magnam mollitia necessitatibus neque nesciunt, obcaecati odio odit quam, repellat repellendus. », par @example.bsky.social à revoir sur le site de l'AFUP",
                new Embed(
                    'https://afup.org/talks/123-lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-ab-accusantium-architecto-assumenda-consequuntur-corporis-dolorem-doloremque-eaque-magnam-mollitia-necessitatibus-neque-nesciunt-obcaecati-odio-odit-quam-repellat-repellendus',
                    'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab accusantium architecto assumenda consequuntur corporis dolorem doloremque eaque magnam mollitia necessitatibus neque nesciunt, obcaecati odio odit quam, repellat repellendus.',
                    'Foo bar',
                    'https://i.ytimg.com/vi_webp/abcd1234/maxresdefault.webp'
                )
            ),
        ];

        yield 'title a bit too long 2' => [
            SocialNetwork::Bluesky,
            (new Talk())
                ->setId(123)
                ->setTitle('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab accusantium architecto assumenda consequuntur corporis dolorem doloremque eaque magnam mollitia necessitatibus neque nesciunt, obcaecati odio odit quam, repellat repellendus magnam.')
                ->setAbstract('Foo bar')
                ->setYoutubeId('abcd1234'),
            [
                (new Speaker())->setBluesky('example.bsky.social'),
            ],
            new Status(
                "« Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab accusantium architecto assumenda consequuntur corporis dolorem doloremque eaque magnam mollitia necessitatibus neque nesciunt, obcaecati odio odit quam, repellat repellendus magnam. », par @example.bsky.social à revoir sur l'AFUP",
                new Embed(
                    'https://afup.org/talks/123-lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-ab-accusantium-architecto-assumenda-consequuntur-corporis-dolorem-doloremque-eaque-magnam-mollitia-necessitatibus-neque-nesciunt-obcaecati-odio-odit-quam-repellat-repellendus-magnam',
                    'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab accusantium architecto assumenda consequuntur corporis dolorem doloremque eaque magnam mollitia necessitatibus neque nesciunt, obcaecati odio odit quam, repellat repellendus magnam.',
                    'Foo bar',
                    'https://i.ytimg.com/vi_webp/abcd1234/maxresdefault.webp'
                )
            ),
        ];

        yield 'title with consecutive spaces' => [
            SocialNetwork::Bluesky,
            (new Talk())
                ->setId(123)
                ->setTitle('  Lorem    ipsum ')
                ->setAbstract('Foo bar')
                ->setYoutubeId('abcd1234'),
            [
                (new Speaker())->setBluesky('example.bsky.social'),
            ],
            new Status(
                "« Lorem ipsum », la conférence de @example.bsky.social à revoir sur le site de l'AFUP",
                new Embed(
                    'https://afup.org/talks/123-lorem-ipsum',
                    'Lorem ipsum',
                    'Foo bar',
                    'https://i.ytimg.com/vi_webp/abcd1234/maxresdefault.webp'
                )
            ),
        ];
    }
}
