<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Ticket\Dto\TicketTypeDetailsCollectionDto;
use AppBundle\Event\Ticket\Dto\TicketTypeDetailsDto;

final readonly class TicketTypeDetailsCollectionFactory
{
    public function __construct(
        private TicketEventTypeRepository $ticketEventTypeRepository,
        private TicketTypeAvailability $ticketTypeAvailability,
    ) {}

    public function create(Event $event): TicketTypeDetailsCollectionDto
    {
        $ticketEventTypes = $this->ticketEventTypeRepository->getTicketsByEvent($event, false);
        $ticketTypeDetailsCollectionDto = $this->createFromGlobals();

        foreach ($ticketEventTypes as $ticketEventType) {
            $id = $ticketEventType->getTicketTypeId();

            $ticketTypeDetailsCollectionDto->addTicketTypeDetails($id,
                new TicketTypeDetailsDto(
                    $id,
                    $ticketEventType->getTicketType()->getPrettyName(),
                    $ticketEventType->getPrice(),
                    $this->ticketTypeAvailability->getStock($ticketEventType, $event),
                ),
                $ticketEventType->getTicketType()->getIsRestrictedToMembers(),
            );
        }


        return $ticketTypeDetailsCollectionDto;
    }

    // Compatibilité Legacy, à supprimer et remplacer par un simple "new TicketTypeDetailsCollectionDto()";
    private function createFromGlobals(): TicketTypeDetailsCollectionDto
    {
        global $AFUP_Tarifs_Forum, $AFUP_Tarifs_Forum_Lib;

        $ticketTypeDetailsCollectionDto = new TicketTypeDetailsCollectionDto();

        foreach ($AFUP_Tarifs_Forum_Lib as $id => $tarifLabel) {
            $ticketTypeDetailsCollectionDto->addTicketTypeDetails(
                $id,
                new TicketTypeDetailsDto(
                    $id,
                    $tarifLabel,
                    $AFUP_Tarifs_Forum[$id] ?? 0,
                    null,
                ),
                false,
            );
        }

        return $ticketTypeDetailsCollectionDto;
    }
}
