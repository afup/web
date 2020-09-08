<?php

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
    /** @var TicketRepository */
    private $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function getStock(TicketEventType $ticketEventType, Event $event)
    {
        // Les tickets depuis les tokens doivent fonctionner même après le sold out
        if ($ticketEventType->getTicketType()->getTechnicalName() === TicketType::SPECIAL_PRICE_TECHNICAL_NAME) {
            return PHP_INT_MAX;
        }

        $maxTickets = $ticketEventType->getMaxTickets();

        if (count($ticketEventType->getTicketType()->getDays()) === 2) {
            list($day1, $day2) = $ticketEventType->getTicketType()->getDays();
            // Two days ticket
            $stock = $event->getSeats() - max(
                    $this->ticketRepository->getPublicSoldTicketsByDay($day1, $event),
                    $this->ticketRepository->getPublicSoldTicketsByDay($day2, $event)
                );
            if (null !== $maxTickets) {
                $maxTickets -= max(
                    $this->ticketRepository->getPublicSoldTicketsByDayOfType($day1, $event, $ticketEventType->getTicketType()),
                    $this->ticketRepository->getPublicSoldTicketsByDayOfType($day2, $event, $ticketEventType->getTicketType())
                );
            }
        } else {
            $stock = $event->getSeats() - $this->ticketRepository->getPublicSoldTicketsByDay($ticketEventType->getTicketType()->getDay(), $event);

            if (null !== $maxTickets) {
                $maxTickets -= $this->ticketRepository->getPublicSoldTicketsByDayOfType($ticketEventType->getTicketType()
                    ->getDay(), $event, $ticketEventType->getTicketType());
            }
        }
        if (null !== $maxTickets) {
            $stock = min($stock, $maxTickets);
        }

        return max($stock, 0);
    }
}
