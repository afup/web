<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket\Dto;

final class TicketTypeDetailsCollectionDto
{
    /** @param array<TicketTypeDetailsDto> $ticketTypeDetailsCollectionDto */
    public function __construct(
        private array $ticketTypeDetailsCollectionDto = [],
        public array $ticketTypeForMemberOnly = [],
    ) {}

    public function addTicketTypeDetails(int $id, TicketTypeDetailsDto $ticketTypeDetailsDto, bool $isForMemberOnly): void
    {
        $this->ticketTypeDetailsCollectionDto[$id] = $ticketTypeDetailsDto;

        if ($isForMemberOnly) {
            $this->ticketTypeForMemberOnly[$id] = $id;
        }
    }

    public function getTicketTypeDetailsCollectionDto(): array
    {
        return $this->ticketTypeDetailsCollectionDto;
    }

    public function getTicketTypeDetails(int $id): ?TicketTypeDetailsDto
    {
        return $this->ticketTypeDetailsCollectionDto[$id] ?? null;
    }
}
