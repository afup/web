<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Inscription;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use AppBundle\Event\Ticket\TicketTypeDetailsCollectionFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __invoke(
        Request $request,
        EventActionHelper $eventActionHelper,
        EventRepository $eventRepository,
        TicketEventTypeRepository $ticketEventTypeRepository,
        TicketTypeAvailability $ticketTypeAvailability,
        EventStatsRepository $eventStatsRepository,
        TicketRepository $ticketRepository,
        EventSelectFactory $eventSelectFactory,
        TicketTypeDetailsCollectionFactory $ticketTypeDetailsCollectionFactory,
    ): Response {
        $id = $request->query->get('id');
        $direction = $request->query->get('direction', 'DESC');
        $sort = $request->query->get('sort', 'date');
        $filter = $request->query->get('filter');

        $event = $eventActionHelper->getEventById($id);

        $stats = $eventStatsRepository->getStats($event->getId());

        return $this->render('event/inscription/list.html.twig', [
            'filter' => $filter,
            'direction' => $direction,
            'sort' => $sort,
            'inscriptions' => $ticketRepository->getTicketsForList($event, $filter, $sort, $direction),
            'statistiques' => $stats,
            'ticketTypesDetailsCollection' => $ticketTypeDetailsCollectionFactory->create($event),
            'event' => $event,
            'event_select_form' => $eventSelectFactory->create($event, $request)->createView(),
        ]);
    }
}
