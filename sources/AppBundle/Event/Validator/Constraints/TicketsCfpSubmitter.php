<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TicketsCfpSubmitter extends Constraint
{
    public $messageTooMuchCfpSubmitterTickets = 'You can only order one ticket "{{ ticket_pretty_name }}".';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
