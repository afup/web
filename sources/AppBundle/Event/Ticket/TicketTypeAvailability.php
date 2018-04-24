<?php

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\TicketEventType;
use AppBundle\Event\Model\TicketType;

class TicketTypeAvailability
{
    const DAY_ONE = 'one';
    const DAY_TWO = 'two';
    const DAY_BOTH = 'both';
    /**
     * @var TicketEventTypeRepository
     */
    private $ticketEventTypeRepository;

    /**
     * @var TicketRepository
     */
    private $ticketRepository;

    public function __construct(TicketEventTypeRepository $ticketEventTypeRepository, TicketRepository $ticketRepository)
    {
        $this->ticketEventTypeRepository = $ticketEventTypeRepository;
        $this->ticketRepository = $ticketRepository;
    }

    public function getStock(TicketEventType $ticketEventType, Event $event)
    {
        // Les tickets depuis les tokens doivent fonctionner même après le sold out
        if ($ticketEventType->getTicketType()->getTechnicalName() == TicketType::SPECIAL_PRICE_TECHNICAL_NAME) {
            return PHP_INT_MAX;
        }

        if (count($ticketEventType->getTicketType()->getDays()) === 2) {
            // Two days ticket
            $stock = $event->getSeats() - max(
                $this->ticketRepository->getPublicSoldTicketsByDay($ticketEventType->getTicketType()->getDays()[0], $event),
                $this->ticketRepository->getPublicSoldTicketsByDay($ticketEventType->getTicketType()->getDays()[1], $event)
                )
            ;
        } else {
            $stock = $event->getSeats() - $this->ticketRepository->getPublicSoldTicketsByDay($ticketEventType->getTicketType()->getDay(), $event);
        }

        if ($stock < 0) {
            $stock = 0;
        }

        return $stock;
    }
}
