<?php

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\User;
use AppBundle\Event\Form\TicketType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MembershipDiscountEligibiliityComputer
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $securityChecker;

    /**
     * @param AuthorizationCheckerInterface $securityChecker
     */
    public function __construct(AuthorizationCheckerInterface $securityChecker)
    {
        $this->securityChecker = $securityChecker;
    }

    /**
     * @param User|null $user
     *
     * @return bool
     */
    public function computeMembershipDiscountEligibility(User $user = null)
    {
        if (null === $user) {
            return false;
        }

        $memberType = TicketType::MEMBER_NOT;

        if (
            $this->securityChecker->isGranted('ROLE_USER', $user)
            &&
            $user->hasRole('ROLE_MEMBER_EXPIRED') === false
        ) {
            if ($user->getCompanyId() > 0) {
                $memberType = TicketType::MEMBER_CORPORATE;
            } else {
                $memberType = TicketType::MEMBER_PERSONAL;
            }
        }

        return $memberType !== TicketType::MEMBER_NOT;
    }
}
