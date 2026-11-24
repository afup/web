<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AvailableTicketValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TicketTypeAvailability $ticketTypeAvailability,
        private readonly EventRepository $eventRepository,
    ) {}

    public function validate($ticket, Constraint $constraint): void
    {
        if (!$constraint instanceof AvailableTicket) {
            throw new UnexpectedTypeException($constraint, AvailableTicket::class);
        }

        if (!($ticket instanceof Inscription) || !$ticket->ticketEventType instanceof TicketEventType) {
            return ;
        }

        $event = $this->eventRepository->get($ticket->ticketEventType->eventId);
        if (
            $ticket->ticketEventType->dateEnd < new \DateTime()
            || $this->ticketTypeAvailability->getStock($ticket->ticketEventType, $event) <= 0
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('ticketEventType')
                ->addViolation();
        }
    }
}
