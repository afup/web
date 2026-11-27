<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\TicketType;

final readonly class TicketAggregate
{
    public function __construct(
        public Inscription $ticket,
        // Renseigné via Doctrine par TicketTypeRepository::enrichTicketAggregates()
        public ?TicketType $ticketType,
        // Renseigné via Ting par InvoiceRepository::enrichTicketAggregates()
        public ?Invoice $invoice,
        public ?\DateTimeImmutable $lastSubscription,
    ) {}
}
