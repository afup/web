<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class PublicTicket
 * @Annotation
 */
class PublicTicket extends Constraint
{
    public $messageNotLoggedIn = 'You must be connected with a valid membership to order this ticket.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
