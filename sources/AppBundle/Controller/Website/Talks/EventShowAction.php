<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Event\Talk\EventTalksJsonLd;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class EventShowAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly EventRepository $eventRepository,
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
        private readonly EventTalksJsonLd $eventTalksJsonLd,
    ) {}

    public function __invoke(string $path): Response
    {
        $event = $this->eventRepository->getByPath($path);
        $eventTalks = null !== $event ? $this->talkRepository->getPublishedByEventWithSpeakers($event) : [];

        if (null === $event || $eventTalks === []) {
            throw $this->createNotFoundException();
        }

        // Slug de la page de chaque speaker, par ID de ligne speaker : un seul index, chargé une fois pour tous.
        $speakerSlugs = [];
        foreach ($eventTalks as $eventTalk) {
            foreach ($eventTalk->speakers as $speaker) {
                $identity = $this->speakerProfileResolver->getIdentityForSpeaker($speaker);
                if (null !== $identity && null !== $speaker->getId()) {
                    $speakerSlugs[$speaker->getId()] = $identity->slug;
                }
            }
        }

        return $this->view->render('site/talks/event.html.twig', [
            'event' => $event,
            'event_talks' => $eventTalks,
            'speaker_slugs' => $speakerSlugs,
            'item_list' => $this->eventTalksJsonLd->getData($event, $eventTalks),
        ]);
    }
}
