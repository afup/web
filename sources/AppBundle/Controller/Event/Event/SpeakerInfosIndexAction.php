<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class SpeakerInfosIndexAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(): Response
    {
        $event = $this->eventRepository->getNextEventForGithubUser($this->getUser());

        if ($event === null) {
            return $this->render('event/none.html.twig');
        }

        return new RedirectResponse($this->generateUrl('speaker-infos', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);
    }
}
