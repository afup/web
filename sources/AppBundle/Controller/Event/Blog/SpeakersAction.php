<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\JsonLd;
use AppBundle\Event\Speaker\ScheduledSpeakersProvider;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SpeakersAction extends AbstractController
{
    public function __construct(
        private readonly JsonLd $jsonLd,
        private readonly EventActionHelper $eventActionHelper,
        private readonly ScheduledSpeakersProvider $scheduledSpeakersProvider,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
    ) {}

    public function __invoke(Request $request, string $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        $speakers = $this->scheduledSpeakersProvider->getScheduledSpeakers((int) $event->getId(), !$request->query->getBoolean('apply-publication-date-filters', true));

        // Slug de la page speaker sur afup.org, pour les seules personnes qui ont un talk publié
        // (sinon la page n'existe pas) : un seul index, chargé une fois pour tous.
        $speakerSlugs = [];
        foreach ($speakers as $row) {
            $speaker = $row['speaker'];
            $identity = $this->speakerProfileResolver->getIdentityForSpeaker($speaker);
            if (null !== $identity) {
                $speakerSlugs[$speaker->id] = $identity->slug;
            }
        }

        $jsonld = $this->jsonLd->getDataForEvent($event);

        return $this->render(
            'blog/speakers.html.twig',
            [
                'speakers' => $speakers,
                'speaker_slugs' => $speakerSlugs,
                'event' => $event,
                'jsonld' => $jsonld,
                'programPagePrefix' => $request->query->get('program-page-prefix', '/' . $event->getPath() . '/programme/'),
            ],
        );
    }
}
