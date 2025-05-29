<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TicketsCfpSubmitter extends Constraint
{
    public string $messageTooMuchCfpSubmitterTickets = 'You can only order one ticket "{{ ticket_pretty_name }}".';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
