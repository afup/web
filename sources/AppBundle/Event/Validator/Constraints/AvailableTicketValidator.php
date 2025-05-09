<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketEventType;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AvailableTicketValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TicketTypeAvailability $ticketTypeAvailability,
        private readonly EventRepository $eventRepository,
    ) {
    }

    public function validate($ticket, Constraint $constraint): void
    {
        if (!$constraint instanceof AvailableTicket) {
            throw new UnexpectedTypeException($constraint, AvailableTicket::class);
        }

        if (!($ticket instanceof Ticket) || !$ticket->getTicketEventType() instanceof TicketEventType) {
            return ;
        }

        $event = $this->eventRepository->get($ticket->getTicketEventType()->getEventId());
        if (
            $ticket->getTicketEventType()->getDateEnd() < new \DateTime()
            ||
            $this->ticketTypeAvailability->getStock($ticket->getTicketEventType(), $event) <= 0
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('ticketEventType')
                ->addViolation();
        }
    }
}
