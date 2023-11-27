<?php

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LoggedInMemberValidator extends ConstraintValidator
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
        if (!$constraint instanceof LoggedInMember) {
            throw new UnexpectedTypeException($constraint, LoggedInMember::class);
        }

        if (!($ticket instanceof Ticket) || $ticket->getTicketEventType() === null || $ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers() === false) {
            return ;
        }

        $token = $this->tokenStorage->getToken();
        $message = null;
        if ($token === null) {
            $message = $constraint->messageNotLoggedIn;
        } elseif ($token->getUser()->hasRole('ROLE_MEMBER_EXPIRED')) {
            $message = $constraint->messageFeeOutOfDate;
        } elseif ($token->getUser()->getEmail() !== $ticket->getEmail()) {
            $message = $constraint->messageBadMail;
        }
        if ($message !== null) {
            $this->context->buildViolation($message)
                ->atPath('email')
                ->addViolation();
        }
    }
}
