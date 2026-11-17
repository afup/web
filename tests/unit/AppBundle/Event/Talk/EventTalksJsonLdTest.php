<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Speaker\PersonJsonLd;
use AppBundle\Event\Talk\EventTalk;
use AppBundle\Event\Talk\EventTalksJsonLd;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class EventTalksJsonLdTest extends TestCase
{
    public function testGetDataListsTheTalksInOrder(): void
    {
        $event = new Event()->setTitle('Forum PHP 2025')->setPath('forumphp2025');

        self::assertSame([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Forum PHP 2025 : les talks et vidéos',
            'url' => 'https://afup.org/talks/evenements/forumphp2025',
            'numberOfItems' => 2,
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'url' => 'https://afup.org/talks/12-keynote-d-ouverture', 'name' => 'Keynote d\'ouverture'],
                ['@type' => 'ListItem', 'position' => 2, 'url' => 'https://afup.org/talks/7-cloture', 'name' => 'Clôture'],
            ],
        ], self::jsonLd()->getData($event, [self::eventTalk(12, 'Keynote d\'ouverture'), self::eventTalk(7, 'Clôture')]));
    }

    public function testEncodedJsonCannotCloseTheScriptTag(): void
    {
        $event = new Event()->setTitle('</script><script>alert(1)</script>')->setPath('x');

        $json = json_encode(self::jsonLd()->getData($event, [self::eventTalk(1, '</script>')]), PersonJsonLd::JSON_FLAGS);

        self::assertIsString($json);
        self::assertStringNotContainsString('<', $json);
        self::assertSame('</script>', json_decode($json, true)['itemListElement'][0]['name']);
    }

    private static function jsonLd(): EventTalksJsonLd
    {
        $routes = new RouteCollection();
        $routes->add('talks_show', new Route('/talks/{id}-{slug}'));
        $routes->add('talks_event', new Route('/talks/evenements/{path}'));

        return new EventTalksJsonLd(new UrlGenerator($routes, new RequestContext(host: 'afup.org', scheme: 'https')));
    }

    private static function eventTalk(int $id, string $title): EventTalk
    {
        return new EventTalk(new Talk()->setId($id)->setTitle($title), new \DateTimeImmutable(), []);
    }
}
