<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Entity\TicketType;

final readonly class TicketAggregate
{
    public function __construct(
        public Ticket $ticket,
        // Renseigné via Doctrine par TicketTypeRepository::enrichTicketAggregates()
        public ?TicketType $ticketType,
        public ?Invoice $invoice,
        public ?\DateTimeImmutable $lastSubscription,
    ) {}
}
