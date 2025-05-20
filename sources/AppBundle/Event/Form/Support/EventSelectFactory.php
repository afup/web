<?php

declare(strict_types=1);

namespace AppBundle\Event\Form\Support;

use AppBundle\Controller\Admin\Event\AdminActionWithEventSelector;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class EventSelectFactory
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EventRepository $eventRepository,
    ) {}

    public function create(Event $event, Request $request): FormInterface
    {
        // L'id dans l'url est prioritaire sur celui de la session
        $selectedEventId = $request->query->get('id') ?? $request->getSession()->get(AdminActionWithEventSelector::SESSION_KEY);

        if ($request->query->has('id')) {
            $request->getSession()->set(AdminActionWithEventSelector::SESSION_KEY, $selectedEventId);
        }

        $selectedEvent = null;
        if ($selectedEventId) {
            $selectedEvent = $this->eventRepository->get($selectedEventId);
        }

        return $this->formFactory->create(EventSelectType::class, $event, [
            'data' => $selectedEvent,
        ]);
    }
}
