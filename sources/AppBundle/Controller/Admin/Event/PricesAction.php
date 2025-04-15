<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PricesAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private TicketEventTypeRepository $ticketEventTypeRepository;

    public function __construct(
        EventActionHelper $eventActionHelper,
        TicketEventTypeRepository $ticketEventTypeRepository
    ) {
        $this->ticketEventTypeRepository = $ticketEventTypeRepository;
        $this->eventActionHelper = $eventActionHelper;
    }

    public function __invoke(Request $request): Response
    {
        $id = $request->query->get('id');
        $event = $this->eventActionHelper->getEventById($id);
        $ticketEventTypes = $this->ticketEventTypeRepository->getTicketsByEvent($event);

        return $this->render('admin/event/prices.html.twig', [
            'ticket_event_types' => $ticketEventTypes,
            'has_prices_defined_with_vat' => $event->hasPricesDefinedWithVat(),
            'event' => $event,
            'title' => 'Liste des prix',
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }
}
