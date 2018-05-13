<?php

namespace AppBundle\Event\Ticket\tests\units;

use AppBundle\Association\Model\User;
use AppBundle\Event\Ticket\MembershipDiscountEligibiliityComputer as TestedClass;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AutorisationChecker implements AuthorizationCheckerInterface
{
    private $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function isGranted($attributes, $object = null)
    {
        return in_array($attributes, $this->allowedRoles);
    }
}

class MembershipDiscountEligibiliityComputer extends \atoum
{
    /**
     * @dataProvider computeDataProvider
     */
    public function testCompute($case, callable $user = null, array $allowedRoles, $userSeniority, $expectedIs, $expectedCompute)
    {
        $this
            ->assert($case)
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $mockSeniority = new \mock\AppBundle\Event\Ticket\SeniorityComputer(),
                $this->calling($mockSeniority)->computeSeniority = function(User $user) use ($userSeniority) {
                    if (null === $userSeniority) {
                        return null;
                    }
                    return new \DateInterval($userSeniority);
                }
            )
            ->when($computer = new TestedClass(new AutorisationChecker($allowedRoles), $mockSeniority))
            ->then
                ->boolean($computer->isEligibleToMembershopDiscount($user()))
                    ->isEqualTo($expectedIs, $case)
                ->integer($computer->computeMembershipDiscountEligibility($user()))
                    ->isEqualTo($expectedCompute, $case)
        ;
    }

    public function computeDataProvider()
    {
        return [
            [
                'case' => 'Sans user',
                'user' => function() { return null; },
                'security_checked_allowed_roles' => [],
                'user_seniority' => null,
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_NOT_CONNECTED,
            ],
            [
                'case' => "Avec un user qui n'a pas le role user",
                'user' => function() { return new User(); },
                'security_checked_allowed_roles' => [],
                'user_seniority' => null,
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_NOT_CONNECTED,
            ],
            [
                'case' => "Avec un user personne physique qui n'a pas le role user et qui n'a pas expiré",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-05-13'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => [],
                'user_seniority' => null,
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_NOT_CONNECTED,
            ],
            [
                'case' => "Avec un user personne physique qui a le role user et qui n'a pas expiré et a plus de 3 mois",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-05-13'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => ['ROLE_USER'],
                'user_seniority' => 'P1Y2M',
                'expected_is' => true,
                'expected_compute' => TestedClass::NO_ERROR,
            ],
            [
                'case' => "Avec un user personne physique qui a le role user et qui n'a pas expiré et a 4 mois",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-05-13'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => ['ROLE_USER'],
                'user_seniority' => 'P4M',
                'expected_is' => true,
                'expected_compute' => TestedClass::NO_ERROR,
            ],
            [
                'case' => "Avec un user personne physique qui a le role user et qui n'a pas expiré et a 3 mois",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-05-13'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => ['ROLE_USER'],
                'user_seniority' => 'P3M',
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_MEMBERSHIP_MINIMUM_MONTHS_NOT_REACHED,
            ],
            [
                'case' => "Avec un user personne physique qui a le role user et qui n'a pas expiré et a 1 mois",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-05-13'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => ['ROLE_USER'],
                'user_seniority' => 'P1M',
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_MEMBERSHIP_MINIMUM_MONTHS_NOT_REACHED,
            ],
            [
                'case' => "Avec un user personne physique qui a le role user et qui n'a pas expiré et a 1 jour",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-05-13'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => ['ROLE_USER'],
                'user_seniority' => 'P1D',
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_MEMBERSHIP_MINIMUM_MONTHS_NOT_REACHED,
            ],
            [
                'case' => "Avec un user personne physique qui a le role user et qui a expiré",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-12-03'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => ['ROLE_USER'],
                'user_seniority' => null,
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_MEMBERSHIP_EXPIRED,
            ],
        ];
    }
}
