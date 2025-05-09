<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\TicketEventType;
use AppBundle\Event\Model\TicketType;

class TicketTypeAvailability
{
    const DAY_ONE = 'one';
    const DAY_TWO = 'two';
    const DAY_BOTH = 'both';

    public function __construct(private readonly TicketRepository $ticketRepository)
    {
    }

    public function getStock(TicketEventType $ticketEventType, Event $event)
    {
        // Les tickets depuis les tokens doivent fonctionner même après le sold out
        if ($ticketEventType->getTicketType()->getTechnicalName() == TicketType::SPECIAL_PRICE_TECHNICAL_NAME) {
            return PHP_INT_MAX;
        }

        // selon si on est sur un ticket un jour ou deux jours, on calcule différement le nombre de tickets vendus, et le nombre de tickets vendus du type
        if (count($ticketEventType->getTicketType()->getDays()) === 2) {
            $allTickets = $this->ticketRepository->getPublicSoldTickets($event);
            $typeTickets = $this->ticketRepository->getPublicSoldTicketsOfType($event, $ticketEventType->getTicketType());
        } else {
            $allTickets = $this->ticketRepository->getPublicSoldTicketsByDay($ticketEventType->getTicketType()->getDay(), $event);
            $typeTickets = $this->ticketRepository->getPublicSoldTicketsByDayOfType($event, $ticketEventType->getTicketType()->getDay(), $ticketEventType->getTicketType());
        }

        // on regarde combien de tickets il nous reste
        $stockTotal = $event->getSeats() - $allTickets;

        if (null !== ($maxTickets = $ticketEventType->getMaxTickets())) {
            $stockForType = $maxTickets - $typeTickets;

            // si on a un maximum sur le nombre de tickets, soit on a tout vendu au global, soit on a tout vendu pour le type de ticket
            $stock = min($stockTotal, $stockForType);
        } else {
            $stock = $stockTotal;
        }

        if ($stock < 0) {
            $stock = 0;
        }

        return $stock;
    }
}
