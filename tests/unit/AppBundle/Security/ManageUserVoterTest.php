<?php

declare(strict_types=1);

namespace AppBundle\Tests\Security;

use AppBundle\Association\Model\User;
use AppBundle\Security\ManageUserVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class ManageUserVoterTest extends TestCase
{
    /**
     * @param list<string> $currentUserRoles
     * @param list<string> $targetRoles
     */
    #[DataProvider('provideCases')]
    public function testVote(array $currentUserRoles, array $targetRoles, int $expected): void
    {
        $roleHierarchy = new RoleHierarchy([
            'ROLE_MEMBRES_WRITER' => ['ROLE_MEMBRES_READER'],
            'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN', 'ROLE_MEMBRES_WRITER'],
        ]);
        $voter = new ManageUserVoter(new AccessDecisionManager([new RoleHierarchyVoter($roleHierarchy)]));

        $token = new UsernamePasswordToken(new InMemoryUser('current', null, $currentUserRoles), 'main', $currentUserRoles);

        $target = new User();
        $target->setRoles($targetRoles);

        self::assertSame($expected, $voter->vote($token, $target, [ManageUserVoter::MANAGE_USER]));
    }

    /**
     * @return iterable<string, array{list<string>, list<string>, int}>
     */
    public static function provideCases(): iterable
    {
        yield 'gestionnaire Membres sur une personne sans rôle' => [['ROLE_MEMBRES_WRITER'], [], VoterInterface::ACCESS_GRANTED];
        yield 'gestionnaire Membres sur un autre gestionnaire' => [['ROLE_MEMBRES_WRITER'], ['ROLE_COMPTA_WRITER'], VoterInterface::ACCESS_GRANTED];
        yield 'gestionnaire Membres sur un super admin' => [['ROLE_MEMBRES_WRITER'], ['ROLE_SUPER_ADMIN'], VoterInterface::ACCESS_DENIED];
        yield 'super admin sur un super admin' => [['ROLE_SUPER_ADMIN'], ['ROLE_SUPER_ADMIN'], VoterInterface::ACCESS_GRANTED];
        yield 'super admin sur une personne sans rôle' => [['ROLE_SUPER_ADMIN'], [], VoterInterface::ACCESS_GRANTED];
    }

    public function testAbstainsOnOtherAttributesAndSubjects(): void
    {
        $voter = new ManageUserVoter(new AccessDecisionManager([]));
        $token = new UsernamePasswordToken(new InMemoryUser('current', null), 'main');

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, new User(), ['OTHER']));
        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, new \stdClass(), [ManageUserVoter::MANAGE_USER]));
    }
}
