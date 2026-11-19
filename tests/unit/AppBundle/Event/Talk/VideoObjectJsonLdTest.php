<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Talk;

use AppBundle\Event\Model\Talk;
use AppBundle\Event\Talk\VideoObjectJsonLd;
use PHPUnit\Framework\TestCase;

final class VideoObjectJsonLdTest extends TestCase
{
    private const string URL = 'https://afup.org/talks/42-rest-ou-graphql';

    public function testGetData(): void
    {
        $talk = new Talk();
        $talk->setTitle('REST ou GraphQL ?');
        $talk->setAbstract("<p>GraphQL est une alternative aux architectures REST pour la r&eacute;alisation d&rsquo;API.</p>\n<p>Second   paragraphe.</p>");
        $talk->setYoutubeId('QhAToFl_Omo');
        $talk->setLanguageCode(Talk::LANGUAGE_CODE_EN);

        $start = new \DateTime('2019-05-16 09:30:00', new \DateTimeZone('Europe/Paris'));

        $data = new VideoObjectJsonLd()->getData($talk, $start, [
            ['start' => 28.0, 'text' => 'Merci.'],
            ['start' => 31.0, 'text' => '<i>Bonjour</i> à toutes et à tous.'],
        ], self::URL);

        self::assertSame([
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => 'REST ou GraphQL ?',
            'description' => 'GraphQL est une alternative aux architectures REST pour la réalisation d’API. Second paragraphe.',
            'thumbnailUrl' => 'https://img.youtube.com/vi/QhAToFl_Omo/hqdefault.jpg',
            'uploadDate' => '2019-05-16T09:30:00+02:00',
            'embedUrl' => 'https://www.youtube.com/embed/QhAToFl_Omo',
            'url' => self::URL,
            'inLanguage' => 'en',
            'transcript' => 'Merci. Bonjour à toutes et à tous.',
        ], $data);
    }

    public function testGetDataWithoutTranscript(): void
    {
        $talk = new Talk();
        $talk->setTitle('Un talk');
        $talk->setAbstract('Un résumé');
        $talk->setYoutubeId('abc');

        $data = new VideoObjectJsonLd()->getData($talk, new \DateTime(), [], self::URL);

        self::assertIsArray($data);
        self::assertArrayNotHasKey('transcript', $data);
        self::assertSame('fr', $data['inLanguage']);
    }

    public function testGetDataWithoutVideo(): void
    {
        $talk = new Talk();
        $talk->setTitle('Un talk');

        self::assertNull(new VideoObjectJsonLd()->getData($talk, new \DateTime(), [], self::URL));

        $talk->setYoutubeId('');
        self::assertNull(new VideoObjectJsonLd()->getData($talk, new \DateTime(), [], self::URL));
    }

    public function testGetDataWithoutPlanningStart(): void
    {
        $talk = new Talk();
        $talk->setTitle('Un talk');
        $talk->setYoutubeId('abc');

        self::assertNull(new VideoObjectJsonLd()->getData($talk, null, [], self::URL));
    }

    public function testEncodedDataCannotCloseTheScriptTag(): void
    {
        $talk = new Talk();
        $talk->setTitle('Titre </script><script>alert(1)</script>');
        $talk->setAbstract('Résumé &lt;/script&gt; échappé en HTML');
        $talk->setYoutubeId('abc');

        $data = new VideoObjectJsonLd()->getData($talk, new \DateTime(), [
            ['start' => 0.0, 'text' => 'Texte &lt;/script&gt;'],
        ], self::URL);
        self::assertIsArray($data);

        $json = json_encode($data, VideoObjectJsonLd::JSON_FLAGS);

        self::assertIsString($json);
        self::assertStringNotContainsStringIgnoringCase('</script', $json);
        self::assertStringNotContainsString('<', $json);
        self::assertSame($data, json_decode($json, true));
    }

    public function testDescriptionFallsBackToTheTitleWhenTheAbstractIsEmpty(): void
    {
        $talk = new Talk();
        $talk->setTitle('Un talk sans résumé');
        $talk->setAbstract('<p> </p>');
        $talk->setYoutubeId('abc');

        $data = new VideoObjectJsonLd()->getData($talk, new \DateTime(), [], self::URL);

        self::assertIsArray($data);
        self::assertSame('Un talk sans résumé', $data['description']);
    }
}
