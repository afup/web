<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Event;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Données structurées schema.org ItemList de la page d'une édition : ses talks, dans l'ordre de la page.
 * À encoder avec PersonJsonLd::JSON_FLAGS.
 *
 * @see https://schema.org/ItemList
 */
final readonly class EventTalksJsonLd
{
    public function __construct(private UrlGeneratorInterface $urlGenerator) {}

    /**
     * @param list<EventTalk> $eventTalks
     *
     * @return array<string, mixed>
     */
    public function getData(Event $event, array $eventTalks): array
    {
        $items = [];
        foreach ($eventTalks as $index => $eventTalk) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => $this->urlGenerator->generate(
                    'talks_show',
                    ['id' => $eventTalk->talk->id, 'slug' => $eventTalk->talk->slug],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
                'name' => $eventTalk->talk->title,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $event->getTitle() . ' : les talks et vidéos',
            'url' => $this->urlGenerator->generate('talks_event', ['path' => $event->getPath()], UrlGeneratorInterface::ABSOLUTE_URL),
            'numberOfItems' => count($items),
            'itemListElement' => $items,
        ];
    }
}
