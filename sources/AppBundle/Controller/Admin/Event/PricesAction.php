<?php

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
    /** @var EventActionHelper */
    private $eventActionHelper;
    /** @var TicketEventTypeRepository */
    private $ticketEventTypeRepository;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var Environment */
    private $twig;

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

    public function __invoke(Request $request)
    {
        $id = $request->query->get('id');
        $event = $this->eventActionHelper->getEventById($id);
        $ticketEventTypes = $this->ticketEventTypeRepository->getTicketsByEvent($event);

        return new Response($this->twig->render('admin/event/prices.html.twig', [
            'ticket_event_types' => $ticketEventTypes,
            'event' => $event,
            'title' => 'Liste des prix',
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }
}
