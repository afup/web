<?php

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CorporateMemberValidator extends ConstraintValidator
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var CompanyMemberRepository
     */
    private $companyMemberRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(TokenStorageInterface $tokenStorage, CompanyMemberRepository $companyMemberRepository, UserRepository $userRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->companyMemberRepository = $companyMemberRepository;
        $this->userRepository = $userRepository;
    }

    public function validate($ticket, Constraint $constraint)
    {
        /**
         * @var $ticket Ticket
         */
        if (!($ticket instanceof Ticket) || $ticket->getTicketEventType() === null || $ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers() === false) {
            return ;
        }
        $token = $this->tokenStorage->getToken();

        /**
         * @var $constraint CorporateMember
         */
        if ($token === null) {
            // Il faut etre connecté pour avoir accès aux tickets membre
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->atPath('ticketTypeId')
                ->addViolation();
        }

        $company = $this->companyMemberRepository->get($token->getUser()->getCompanyId());

        if ($company === null) {
            // Il faut etre connecté pour avoir accès aux tickets membre
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->atPath('ticketTypeId')
                ->addViolation();
        }

        $users = $this->userRepository->loadActiveUsersByCompany($company);
        $foundUser = false;
        foreach ($users as $user) {
            if ($user->getEmail() === $ticket->getEmail()) {
                $foundUser = true;
            }
        }
        if ($foundUser === false) {
            $this->context->buildViolation($constraint->messageBadMail)
                ->atPath('email')
                ->addViolation();
        }
    }
}
