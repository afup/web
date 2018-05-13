<?php

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Ticket\MembershipDiscountEligibiliityComputer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LoggedInMemberValidator extends ConstraintValidator
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var MembershipDiscountEligibiliityComputer
     */
    private $discountEligibiliityComputer;

    public function __construct(TokenStorageInterface $tokenStorage, MembershipDiscountEligibiliityComputer $discountEligibiliityComputer)
    {
        $this->tokenStorage = $tokenStorage;
        $this->discountEligibiliityComputer = $discountEligibiliityComputer;
    }

    public function validate($ticket, Constraint $constraint)
    {
        /**
         * @var $ticket Ticket
         */
        if (!($ticket instanceof Ticket) || $ticket->getTicketEventType() === null || $ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers() === false) {
            return ;
        }

        /**
         * @var $constraint LoggedInMember
         */
        $token = $this->tokenStorage->getToken();

        $membershipEligibilityErrors = $this->discountEligibiliityComputer->computeMembershipDiscountEligibility($token->getUser());

        $message = null;
        if (MembershipDiscountEligibiliityComputer::USER_NOT_CONNECTED === $membershipEligibilityErrors) {
            $message = $constraint->messageNotLoggedIn;
        } elseif (MembershipDiscountEligibiliityComputer::USER_MEMBERSHIP_EXPIRED === $membershipEligibilityErrors) {
            $message = $constraint->messageFeeOutOfDate;
        } elseif (MembershipDiscountEligibiliityComputer::USER_MEMBERSHIP_MINIMUM_MONTHS_NOT_REACHED === $membershipEligibilityErrors) {
            $message = $constraint->messageMinimumMembership;
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
