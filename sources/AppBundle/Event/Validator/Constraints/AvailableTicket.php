<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AvailableTicket extends Constraint
{
    public string $message = 'This ticket is not available anymore.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
