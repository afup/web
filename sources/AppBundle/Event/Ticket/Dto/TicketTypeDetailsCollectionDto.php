<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket\Dto;

final class TicketTypeDetailsCollectionDto
{
    /**
     * @param array<TicketTypeDetailsDto> $ticketTypeDetailsCollectionDto
     * @param array<int, int> $ticketTypeForMemberOnly
     */
    public function __construct(
        private array $ticketTypeDetailsCollectionDto = [],
        private array $ticketTypeForMemberOnly = [],
    ) {}

    public function addTicketTypeDetails(TicketTypeDetailsDto $ticketTypeDetailsDto, bool $isForMemberOnly): void
    {
        $id = $ticketTypeDetailsDto->id;
        $this->ticketTypeDetailsCollectionDto[$id] = $ticketTypeDetailsDto;

        if ($isForMemberOnly) {
            $this->ticketTypeForMemberOnly[$id] = $id;
        }
    }

    public function getTicketTypeForMemberOnly(): array
    {
        return $this->ticketTypeForMemberOnly;
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
