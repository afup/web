<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Entity\Inscription;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EarlyBirdTicketValidator extends ConstraintValidator
{
    /**
     * @param Inscription[] $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EarlyBirdTicket) {
            throw new UnexpectedTypeException($constraint, EarlyBirdTicket::class);
        }

        $count = 0;

        foreach ($value as $index => $ticket) {
            if ($ticket->ticketEventType?->ticketType !== null
                && $ticket->ticketEventType->ticketType->isEarly()) {
                $count++;

                // On autorise qu'un seul ticket
                if ($count > 1) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ ticket_pretty_name }}', $ticket->ticketEventType->ticketType->prettyName)
                        ->atPath('[' . $index . '].ticketEventType')
                        ->addViolation()
                    ;
                }
            }
        }
    }
}
