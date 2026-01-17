<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

class TicketOffer
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
        public int $availableTickets,
        public Event $event,
    ) {}
}
