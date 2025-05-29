<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class LoggedInMember extends Constraint
{
    public string $messageNotLoggedIn = 'You must be connected to order this ticket.';
    public string $messageBadMail = 'You must use the same email for your ticket and you membership.';
    public string $messageFeeOutOfDate = 'You must have paid your membership fee to order this ticket.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
