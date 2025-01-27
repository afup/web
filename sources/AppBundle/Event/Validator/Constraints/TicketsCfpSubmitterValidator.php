<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Ticket;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TicketsCfpSubmitterValidator extends ConstraintValidator
{
    /**
     * @param Ticket[] $value
     * @param TicketsCfpSubmitter $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        $specialCFPSubmitter = 0;

        foreach ($value as $index => $ticket) {
            if ($ticket->getTicketEventType() &&
                $ticket->getTicketEventType()->getTicketType() &&
                $ticket->getTicketEventType()->getTicketType()->getIsRestrictedToCfpSubmitter()) {
                $specialCFPSubmitter++;

                // On autorise qu'un seul ticket au tarif CFP submitter
                if ($specialCFPSubmitter > 1) {
                    $this->context->buildViolation($constraint->messageTooMuchCfpSubmitterTickets)
                        ->setParameter('{{ ticket_pretty_name }}', $ticket->getTicketEventType()->getTicketType()->getPrettyName())
                        ->atPath('[' . $index . '].ticketEventType')
                        ->addViolation()
                    ;
                }
            }
        }
    }
}
