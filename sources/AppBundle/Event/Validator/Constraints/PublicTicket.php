<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PublicTicket extends Constraint
{
    public string $messageNotLoggedIn = 'You must be connected with a valid membership to order this ticket.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
