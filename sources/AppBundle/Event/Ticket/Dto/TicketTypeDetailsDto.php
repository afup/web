<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket\Dto;

final class TicketTypeDetailsDto
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
        public ?int $remainingTickets,
    ) {}
}
