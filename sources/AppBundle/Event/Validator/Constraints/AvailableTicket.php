<?php

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class PublicTicket
 * @Annotation
 */
class AvailableTicket extends Constraint
{
    public $message = 'This ticket is not available anymore.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
