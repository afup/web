<?php

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PublicTicketValidator extends ConstraintValidator
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function validate($ticket, Constraint $constraint)
    {
        /**
         * @var $ticket Ticket
         */
        if (!($ticket instanceof Ticket) || $ticket->getTicketEventType() === null) {
            return ;
        }

        /**
         * @var $constraint PublicTicket
         */
        if ($ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers() === true) {
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->atPath('email')
                ->addViolation();
        }

    }
}
