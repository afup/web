<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CorporateMember
 * @Annotation
 */
class CorporateMember extends Constraint
{
    public $messageNotLoggedIn = 'You must be connected to order this ticket.';
    public $messageFeeOutOfDate = 'You must have paid your membership fee to order this ticket.';
    public $messageTooMuchRestrictedTickets = 'You cannot order as many tickets at the discounted rate.';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
