<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

final readonly class TicketOffer
{
    public function __construct(
        public int $ticketTypeId,
        public string $name,
        public float $price,
        public int $availableTickets,
        public ?Event $event = null,
        public ?TicketEventType $ticketEventType = null,
    ) {}
}
