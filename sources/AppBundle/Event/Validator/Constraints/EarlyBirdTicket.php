<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EarlyBirdTicket extends Constraint
{
    public $message = 'You can only order one early bird ticket "{{ ticket_pretty_name }}".';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
