<?php

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Ticket;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PublicTicketValidator extends ConstraintValidator
{
    public function validate($ticket, Constraint $constraint)
    {
        if (!$constraint instanceof PublicTicket) {
            throw new UnexpectedTypeException($constraint, PublicTicket::class);
        }

        if (!($ticket instanceof Ticket) || $ticket->getTicketEventType() === null) {
            return ;
        }

        if ($ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers() === true) {
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->atPath('email')
                ->addViolation();
        }
    }
}
