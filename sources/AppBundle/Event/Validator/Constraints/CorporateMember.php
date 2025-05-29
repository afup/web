<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CorporateMember extends Constraint
{
    public string $messageNotLoggedIn = 'You must be connected to order this ticket.';
    public string $messageFeeOutOfDate = 'You must have paid your membership fee to order this ticket.';
    public string $messageTooMuchRestrictedTickets = 'You cannot order as many tickets at the discounted rate.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
