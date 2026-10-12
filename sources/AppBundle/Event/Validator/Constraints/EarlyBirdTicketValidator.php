<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Ticket;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EarlyBirdTicketValidator extends ConstraintValidator
{
    /**
     * @param Ticket[] $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EarlyBirdTicket) {
            throw new UnexpectedTypeException($constraint, EarlyBirdTicket::class);
        }

        $count = 0;

        foreach ($value as $index => $ticket) {
            if ($ticket->getTicketEventType()?->ticketType !== null
                && $ticket->getTicketEventType()->ticketType->isEarly()) {
                $count++;

                // On autorise qu'un seul ticket
                if ($count > 1) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ ticket_pretty_name }}', $ticket->getTicketEventType()->ticketType->getPrettyName())
                        ->atPath('[' . $index . '].ticketEventType')
                        ->addViolation()
                    ;
                }
            }
        }
    }
}
