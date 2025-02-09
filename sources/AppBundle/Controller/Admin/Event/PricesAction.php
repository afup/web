<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PricesAction
{
    private EventActionHelper $eventActionHelper;
    private TicketEventTypeRepository $ticketEventTypeRepository;
    private FormFactoryInterface $formFactory;
    private Environment $twig;

    public function __construct(
        EventActionHelper $eventActionHelper,
        TicketEventTypeRepository $ticketEventTypeRepository,
        FormFactoryInterface $formFactory,
        Environment $twig
    ) {
        $this->ticketEventTypeRepository = $ticketEventTypeRepository;
        $this->twig = $twig;
        $this->eventActionHelper = $eventActionHelper;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request): Response
    {
        $id = $request->query->get('id');
        $event = $this->eventActionHelper->getEventById($id);
        $ticketEventTypes = $this->ticketEventTypeRepository->getTicketsByEvent($event);

        return new Response($this->twig->render('admin/event/prices.html.twig', [
            'ticket_event_types' => $ticketEventTypes,
            'has_prices_defined_with_vat' => $event->hasPricesDefinedWithVat(),
            'event' => $event,
            'title' => 'Liste des prix',
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }
}
