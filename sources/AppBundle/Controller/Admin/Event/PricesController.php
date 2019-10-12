<?php

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PricesController extends Controller
{
    public function indexAction(Request $request)
    {
        $event = $this->getEvent($this->get(EventRepository::class), $request);

        $ticketEventTypes = $this->get('ting')->get(TicketEventTypeRepository::class)->getTicketsByEvent($event);

        return $this->render(':admin/event:prices.html.twig', [
            'ticket_event_types' => $ticketEventTypes,
            'event' => $event,
            'title' => 'Liste des prix',
        ]);
    }

    private function getEvent(EventRepository $eventRepository, Request $request)
    {
        $event = null;
        if ($request->query->has('id') === false) {
            $event = $eventRepository->getNextEvent();
            $event = $eventRepository->get($event->getId());
        } else {
            $id = $request->query->getInt('id');
            $event = $eventRepository->get($id);
        }

        return $event;
    }
}
