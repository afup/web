<?php

declare(strict_types=1);

namespace AppBundle\Event\Validator\Constraints;

use AppBundle\Association\MemberType;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\User;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\TicketEventType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CorporateMemberValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly InscriptionRepository $inscriptionRepository,
    ) {}

    public function validate($tickets, Constraint $constraint): void
    {
        if (!$constraint instanceof CorporateMember) {
            throw new UnexpectedTypeException($constraint, CorporateMember::class);
        }

        $restrictedTickets = 0;
        $eventId = null;

        foreach ($tickets as $ticket) {
            if (!($ticket instanceof Inscription) || !$ticket->ticketEventType instanceof TicketEventType || $ticket->ticketEventType->ticketType->isRestrictedToMembers === false) {
                continue;
            }
            if ($eventId === null) {
                $eventId = $ticket->ticketEventType->eventId;
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

        $company = $this->companyMemberRepository->find($user->getCompanyId());

        if ($company === null) {
            // Il faut etre connecté pour avoir accès aux tickets membre
            $this->context->buildViolation($constraint->messageNotLoggedIn)
                ->addViolation();
            return ;
        }

        $ticketsSoldToThisCompany = $this->inscriptionRepository->getTotalOfSoldTicketsByMember(
            MemberType::MemberCompany->value,
            $company->id,
            (int) $eventId,
        );

        if (($ticketsSoldToThisCompany + $restrictedTickets) > $company->maxMembers) {
            $this->context->buildViolation($constraint->messageTooMuchRestrictedTickets)
                ->atPath('tickets')
                ->addViolation()
            ;
        }
    }
}
