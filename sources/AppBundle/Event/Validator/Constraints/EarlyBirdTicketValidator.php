<?php

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Ticket;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EarlyBirdTicketValidator extends ConstraintValidator
{
    /**
     * @param Ticket[] $value
     * @param EarlyBirdTicket $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        $count = 0;

        foreach ($value as $index => $ticket) {
            if ($ticket->getTicketEventType() &&
                $ticket->getTicketEventType()->getTicketType() &&
                $ticket->getTicketEventType()->getTicketType()->isEarly()) {
                $count++;

                // On autorise qu'un seul ticket
                if ($count > 1) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ ticket_pretty_name }}', $ticket->getTicketEventType()->getTicketType()->getPrettyName())
                        ->atPath('[' . $index . '].ticketEventType')
                        ->addViolation()
                    ;
                }
            }
        }
    }
}
