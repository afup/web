<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class LoggedInMember
 * @Annotation
 */
class LoggedInMember extends Constraint
{
    public $messageNotLoggedIn = 'You must be connected to order this ticket.';
    public $messageBadMail = 'You must use the same email for your ticket and you membership.';
    public $messageFeeOutOfDate = 'You must have paid your membership fee to order this ticket.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
