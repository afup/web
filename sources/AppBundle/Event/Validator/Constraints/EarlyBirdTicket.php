<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EarlyBirdTicket extends Constraint
{
    public string $message = 'You can only order one early bird ticket "{{ ticket_pretty_name }}".';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
