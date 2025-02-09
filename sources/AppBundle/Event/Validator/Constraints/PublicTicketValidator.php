<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketEventType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PublicTicketValidator extends ConstraintValidator
{
    public function validate($ticket, Constraint $constraint): void
    {
        if (!$constraint instanceof PublicTicket) {
            throw new UnexpectedTypeException($constraint, PublicTicket::class);
        }

        if (!($ticket instanceof Ticket) || !$ticket->getTicketEventType() instanceof TicketEventType) {
            return ;
        }

        if ($ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers() === true) {
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->atPath('email')
                ->addViolation();
        }
    }
}
