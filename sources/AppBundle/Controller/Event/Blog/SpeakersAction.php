<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\JsonLd;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SpeakersAction extends AbstractController
{
    public function __construct(
        private readonly JsonLd $jsonLd,
        private readonly EventActionHelper $eventActionHelper,
        private readonly SpeakerRepository $speakerRepository,
    ) {}

    public function __invoke(Request $request, string $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        $speakers = $this->speakerRepository->getScheduledSpeakersByEvent($event, !$request->query->getBoolean('apply-publication-date-filters', true));
        $jsonld = $this->jsonLd->getDataForEvent($event);

        return $this->render(
            'blog/speakers.html.twig',
            [
                'speakers' => iterator_to_array($speakers),
                'event' => $event,
                'jsonld' => $jsonld,
                'programPagePrefix' => $request->query->get('program-page-prefix', '/' . $event->getPath() . '/programme/'),
            ],
        );
    }
}
