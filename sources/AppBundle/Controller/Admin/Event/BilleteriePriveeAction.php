<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Repository\BilleteriePriveeRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BilleteriePriveeAction extends AbstractController
{
    public function __construct(
        private readonly BilleteriePriveeRepository $billeteriePriveeRepository,
        private readonly TicketTypeRepository $ticketTypeRepository,
    ) {}

    public function __invoke(AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;

        $billeteries = [];
        foreach ($this->billeteriePriveeRepository->findByEvent((int) $event->getId()) as $billeterie) {
            $ticketType = $this->ticketTypeRepository->get($billeterie->ticketTypeId);
            $billeteries[] = [
                'billeterie_privee' => $billeterie,
                'ticket_type' => $ticketType !== null ? $ticketType->getPrettyName() : '',
                'places_prises' => $this->billeteriePriveeRepository->countPlacesPrisesParToken($billeterie->token),
            ];
        }

        return $this->render('admin/event/billeterie_privee.html.twig', [
            'billeteries_privees' => $billeteries,
            'event' => $event,
            'title' => 'Billeteries privées',
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
