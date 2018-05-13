<?php

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\User;
use AppBundle\Event\Form\TicketType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MembershipDiscountEligibiliityComputer
{
    const NO_ERROR = 0;
    const USER_NOT_CONNECTED = 1;
    const USER_MEMBERSHIP_EXPIRED = 2;

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
            return self::USER_NOT_CONNECTED;
        }

        if (!$this->securityChecker->isGranted('ROLE_USER', $user)) {
            return self::USER_NOT_CONNECTED;
        }

        if ($user->hasRole('ROLE_MEMBER_EXPIRED')) {
            return self::USER_MEMBERSHIP_EXPIRED;
        }

        return self::NO_ERROR;
    }

    /**
     * @param User|null $user
     * @return bool true
     */
    public function isEligibleToMembershopDiscount(User $user = null)
    {
        return self::NO_ERROR === $this->computeMembershipDiscountEligibility($user);
    }
}
