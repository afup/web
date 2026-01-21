<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\TicketOffer;

readonly class TicketOffers
{
    public function __construct(
        private TicketEventTypeRepository $ticketEventTypeRepository,
        private TicketTypeAvailability $ticketTypeAvailability,
    ) {}

    /**
     * @return array<TicketOffer>
     */
    public function getAllOffersForEvent(Event $event): array
    {
        global $AFUP_Tarifs_Forum, $AFUP_Tarifs_Forum_Lib;

        $ticketTypes = $this->ticketEventTypeRepository->getTicketsByEvent($event, false);

        $offers = [];
        foreach ($AFUP_Tarifs_Forum as $ticketType => $ticketPrice) {
            $offers[$ticketType] = new TicketOffer(
                (int) $ticketType,
                $AFUP_Tarifs_Forum_Lib[$ticketType],
                $ticketPrice,
                $event->getSeats(),
                $event,
            );
        }

        foreach ($ticketTypes as $ticketType) {
            $offers[$ticketType->getTicketTypeId()] = new TicketOffer(
                $ticketType->getTicketTypeId(),
                $ticketType->getTicketType()->getPrettyName(),
                $ticketType->getPrice(),
                $this->ticketTypeAvailability->getStock($ticketType, $event),
                $event,
            );
        }

        return $offers;
    }

}
