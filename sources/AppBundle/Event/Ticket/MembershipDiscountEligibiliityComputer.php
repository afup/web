<?php

namespace AppBundle\Event\Ticket;

use AppBundle\Association\Model\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MembershipDiscountEligibiliityComputer
{
    const MINIMUM_MEMBERSHIP_MONTHS_FOR_DISCOUNT_ELIGIBILITY = 3;

    const NO_ERROR = 0;
    const USER_NOT_CONNECTED = 1;
    const USER_MEMBERSHIP_EXPIRED = 2;
    const USER_MEMBERSHIP_MINIMUM_MONTHS_NOT_REACHED = 4;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $securityChecker;

    /**
     * @var SeniorityComputer
     */
    private $seniorityComputer;

    /**
     * @param AuthorizationCheckerInterface $securityChecker
     * @param SeniorityComputer $seniorityComputer
     */
    public function __construct(AuthorizationCheckerInterface $securityChecker, SeniorityComputer $seniorityComputer)
    {
        $this->securityChecker = $securityChecker;
        $this->seniorityComputer = $seniorityComputer;
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

        $seniority = $this->seniorityComputer->computeSeniority($user);
        $seniorityMonths = $seniority->m + ($seniority->y * 12);

        if ($seniorityMonths <= self::MINIMUM_MEMBERSHIP_MONTHS_FOR_DISCOUNT_ELIGIBILITY) {
            return self::USER_MEMBERSHIP_MINIMUM_MONTHS_NOT_REACHED;
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
