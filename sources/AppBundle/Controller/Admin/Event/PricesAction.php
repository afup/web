<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PricesAction extends AbstractController
{
    public function __construct(
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
    ) {}

    public function __invoke(AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;
        $ticketEventTypes = $this->ticketEventTypeRepository->getTicketsByEvent($event);

        return $this->render('admin/event/prices.html.twig', [
            'ticket_event_types' => $ticketEventTypes,
            'has_prices_defined_with_vat' => $event->hasPricesDefinedWithVat(),
            'event' => $event,
            'title' => 'Liste des prix',
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
