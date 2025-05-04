<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketEventType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CorporateMemberValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    public function validate($tickets, Constraint $constraint): void
    {
        if (!$constraint instanceof CorporateMember) {
            throw new UnexpectedTypeException($constraint, CorporateMember::class);
        }

        $restrictedTickets = 0;
        $eventId = null;

        foreach ($tickets as $ticket) {
            if (!($ticket instanceof Ticket) || !$ticket->getTicketEventType() instanceof TicketEventType || $ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers() === false) {
                continue;
            }
            if ($eventId === null) {
                $eventId = $ticket->getTicketEventType()->getEventId();
            }
            $restrictedTickets++;
        }

        if ($restrictedTickets === 0) {
            return ;
        }

        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            // Il faut etre connecté pour avoir accès aux tickets membre
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->addViolation()
            ;
            return ;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // Il faut etre connecté pour avoir accès aux tickets membre
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->addViolation()
            ;
            return;
        }

        $company = $this->companyMemberRepository->get($user->getCompanyId());

        if ($company === null) {
            // Il faut etre connecté pour avoir accès aux tickets membre
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->addViolation();
            return ;
        }

        $ticketsSoldToThisCompany = $this->ticketRepository->getTotalOfSoldTicketsByMember(
            UserRepository::USER_TYPE_COMPANY,
            $company->getId(),
            $eventId
        );

        if (($ticketsSoldToThisCompany + $restrictedTickets) > $company->getMaxMembers()) {
            $this->context->buildViolation($constraint->messageTooMuchRestrictedTickets)
                ->atPath('tickets')
                ->addViolation()
            ;
        }
    }
}
