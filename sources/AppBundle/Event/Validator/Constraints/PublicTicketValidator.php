<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\TicketEventType;
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

        if (!($ticket instanceof Inscription) || !$ticket->ticketEventType instanceof TicketEventType) {
            return ;
        }

        if ($ticket->ticketEventType->ticketType->isRestrictedToMembers === true) {
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->atPath('email')
                ->addViolation();
        }
    }
}
