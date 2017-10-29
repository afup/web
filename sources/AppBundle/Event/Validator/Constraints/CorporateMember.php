<?php

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CorporateMember
 * @Annotation
 */
class CorporateMember extends Constraint
{
    public $messageNotLoggedIn = 'You must be connected to order this ticket.';
    public $messageBadMail = 'This email is not a valid member of your company. Please check your membership.';
    public $messageFeeOutOfDate = 'You must have paid your membership fee to order this ticket.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
