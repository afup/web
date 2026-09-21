<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Entity\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\TicketOffer;

readonly class TicketOffers
{
    public function __construct(
        private TicketEventTypeRepository $ticketEventTypeRepository,
        private TicketTypeAvailability $ticketTypeAvailability,
        private TicketTypeRepository $ticketTypeRepository,
    ) {}

    /**
     * @return array<TicketOffer>
     */
    public function getAllOffersForEvent(Event $event): array
    {
        $offers = [];

        foreach ($this->ticketTypeRepository->findAllOrderedById() as $ticketType) {
            $offers[$ticketType->id] = new TicketOffer(
                $ticketType->id,
                $ticketType->prettyName,
                $ticketType->defaultPrice,
                $event->getSeats(),
            );
        }

        $ticketTypes = $this->ticketEventTypeRepository->getTicketsByEvent($event, false);
        foreach ($ticketTypes as $ticketEventType) {
            $ticketTypeId = $ticketEventType->getTicketTypeId();
            $offers[$ticketTypeId] = new TicketOffer(
                $ticketTypeId,
                $ticketEventType->getTicketType()->getPrettyName(),
                $ticketEventType->getPrice(),
                $this->ticketTypeAvailability->getStock($ticketEventType, $event),
                $event,
                $ticketEventType,
            );
        }

        return $offers;
    }

}
