<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\Event\Model\Event;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TalksExtension extends AbstractExtension
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('talks_event_url', $this->eventUrl(...)),
        ];
    }

    /**
     * URL absolue de la liste des talks d'une édition. Seul endroit où elle est générée :
     * la page événement dédiée la remplacera.
     */
    public function eventUrl(Event $event): string
    {
        return $this->urlGenerator->generate(
            'talks_list',
            ['fR' => ['event.title' => [$event->getTitle()]]],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
