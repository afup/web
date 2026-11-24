<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Entity\Inscription;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TicketsCfpSubmitterValidator extends ConstraintValidator
{
    /**
     * @param Inscription[] $value
     * @param TicketsCfpSubmitter $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        $specialCFPSubmitter = 0;

        foreach ($value as $index => $ticket) {
            if ($ticket->ticketEventType?->ticketType !== null
                && $ticket->ticketEventType->ticketType->isRestrictedToCfpSubmitter) {
                $specialCFPSubmitter++;

                // On autorise qu'un seul ticket au tarif CFP submitter
                if ($specialCFPSubmitter > 1) {
                    $this->context->buildViolation($constraint->messageTooMuchCfpSubmitterTickets)
                        ->setParameter('{{ ticket_pretty_name }}', $ticket->ticketEventType->ticketType->prettyName)
                        ->atPath('[' . $index . '].ticketEventType')
                        ->addViolation()
                    ;
                }
            }
        }
    }
}
