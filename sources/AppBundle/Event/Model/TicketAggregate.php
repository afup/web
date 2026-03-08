<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

final readonly class TicketAggregate
{
    public function __construct(
        public Ticket $ticket,
        public TicketType $ticketType,
        public ?Invoice $invoice,
        public ?\DateTimeImmutable $lastSubscription,
    ) {}
}
