<?php

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class PublicTicket
 * @Annotation
 */
class PublicTicket extends Constraint
{
    public $messageNotLoggedIn = 'To order this ticket you must be logged in with a valid membership.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
