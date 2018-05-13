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
    public function testCompute($case, callable $user = null, array $allowedRoles, $expectedIs, $expectedCompute)
    {
        $this
            ->assert($case)
            ->given($computer = new TestedClass(new AutorisationChecker($allowedRoles)))
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
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_NOT_CONNECTED,
            ],
            [
                'case' => "Avec un user qui n'a pas le role user",
                'user' => function() { return new User(); },
                'security_checked_allowed_roles' => [],
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
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_NOT_CONNECTED,
            ],
            [
                'case' => "Avec un user personne physique qui a le role user et qui n'a pas expiré",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-05-13'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => ['ROLE_USER'],
                'expected_is' => true,
                'expected_compute' => TestedClass::NO_ERROR,
            ],
            [
                'case' => "Avec un user personne physique qui a le role user et qui a expiré",
                'user' => function() {
                    $user = new User($currentDate = new \DateTime('2018-12-03'));
                    $user->setLastSubscription((new \DateTime('2018-12-01'))->format('U'));
                    return $user;
                },
                'security_checked_allowed_roles' => ['ROLE_USER'],
                'expected_is' => false,
                'expected_compute' => TestedClass::USER_MEMBERSHIP_EXPIRED,
            ],
        ];
    }
}
