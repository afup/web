<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Association\Model\User;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\TicketEventType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LoggedInMemberValidator extends ConstraintValidator
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage) {}

    public function validate($ticket, Constraint $constraint): void
    {
        if (!$constraint instanceof LoggedInMember) {
            throw new UnexpectedTypeException($constraint, LoggedInMember::class);
        }

        if (!($ticket instanceof Inscription) || !$ticket->ticketEventType instanceof TicketEventType || $ticket->ticketEventType->ticketType->isRestrictedToMembers === false) {
            return ;
        }

        $token = $this->tokenStorage->getToken();
        $user = null;
        $message = null;

        if ($token !== null) {
            $user = $token->getUser();
        }

        if (!$user instanceof User) {
            $message = $constraint->messageNotLoggedIn;
        } elseif ($user->hasRole('ROLE_MEMBER_EXPIRED')) {
            $message = $constraint->messageFeeOutOfDate;
        } elseif ($user->getEmail() !== $ticket->email) {
            $message = $constraint->messageBadMail;
        }

        if ($message !== null) {
            $this->context->buildViolation($message)
                ->atPath('email')
                ->addViolation();
        }
    }
}
