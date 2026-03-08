<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Controller\Admin\Event\RedirectEventFromSessionListener;
use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class EventActionHelper
{
    public function __construct(
        private EventRepository $eventRepository,
        private FormFactoryInterface $formFactory,
        private RequestStack $requestStack,
    ) {}

    /**
     * @throws NotFoundHttpException
     */
    public function getEvent(string $eventSlug): Event
    {
        $event = $this->eventRepository->getOneBy(['path' => $eventSlug]);
        if ($event === null) {
            throw new NotFoundHttpException('Event not found');
        }

        return $event;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function getEventById(int|string|null $id = null, bool $allowFallback = true): Event
    {
        $event = null;
        if (null !== $id) {
            $event = $this->eventRepository->get((int) $id);
        } elseif ($allowFallback) {
            $event = $this->getFromRequest('id', $allowFallback)->event;
        }

        if ($event === null) {
            throw new NotFoundHttpException('Could not find event');
        }

        return $event;
    }

    public function getFromRequest(string $queryParamName, bool $allowFallback = true): AdminEventSelection
    {
        $request = $this->requestStack->getMainRequest();

        // L'id dans l'URL est prioritaire sur celui de la session
        $selectedEventId = $request->query->get($queryParamName) ?? $request->getSession()->get(RedirectEventFromSessionListener::SESSION_KEY);

        // Si l'id est présent dans l'URL, il est stocké en session
        if ($request->query->has($queryParamName)) {
            $request->getSession()->set(RedirectEventFromSessionListener::SESSION_KEY, $selectedEventId);
        }

        $selectedEvent = $this->eventRepository->get($selectedEventId);
        if ($selectedEvent === null && $allowFallback) {
            $selectedEvent = $this->eventRepository->getNextEvent() ?? $this->eventRepository->getLastEvent();
        }

        if ($selectedEvent === null) {
            if ($request->query->has($queryParamName)) {
                // Si l'id n'existe pas, erreur 404
                throw new NotFoundHttpException("Event $selectedEventId inexistant");
            }

            throw new NotFoundHttpException("Impossible de trouver le bon event");
        }

        return new AdminEventSelection(
            $this->formFactory,
            $selectedEvent,
        );
    }
}
