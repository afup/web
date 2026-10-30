<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association\Model;

use AppBundle\Association\Model\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public static function provideModuleRoles(): iterable
    {
        foreach (array_keys(User::MODULES) as $module) {
            yield $module => [$module, 'ROLE_' . strtoupper($module)];
        }
    }

    private function user(): User
    {
        $user = new User();
        $user->setId(1);
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setRoles([]);
        $user->setLastSubscription((string) (new \DateTime('+1 year'))->getTimestamp());

        return $user;
    }

    #[DataProvider('provideModuleRoles')]
    public function testNewUserHasNoAccess(string $module, string $rolePrefix): void
    {
        $user = $this->user();

        self::assertSame(User::LEVEL_NONE, $user->getModuleLevel($module));
        self::assertNotContains($rolePrefix . '_READER', $user->getRoles());
        self::assertNotContains('ROLE_ADMIN', $user->getRoles());
    }

    #[DataProvider('provideModuleRoles')]
    public function testReaderLevelGrantsReaderRole(string $module, string $rolePrefix): void
    {
        $user = $this->user();
        $user->setModuleLevel($module, User::LEVEL_READER);

        $roles = $user->getRoles();
        self::assertContains($rolePrefix . '_READER', $roles);
        self::assertNotContains($rolePrefix . '_WRITER', $roles);
        self::assertSame(User::LEVEL_READER, $user->getModuleLevel($module));
    }

    #[DataProvider('provideModuleRoles')]
    public function testManagerLevelGrantsWriterRole(string $module, string $rolePrefix): void
    {
        $user = $this->user();
        $user->setModuleLevel($module, User::LEVEL_READER);
        $user->setModuleLevel($module, User::LEVEL_MANAGER);

        $roles = $user->getRoles();
        self::assertContains($rolePrefix . '_WRITER', $roles);
        self::assertNotContains($rolePrefix . '_READER', $roles, 'Le WRITER suffit via la hiérarchie WRITE ⇒ READ');
        self::assertSame(User::LEVEL_MANAGER, $user->getModuleLevel($module));
    }

    #[DataProvider('provideModuleRoles')]
    public function testNoneLevelRemovesModuleRoles(string $module, string $rolePrefix): void
    {
        $user = $this->user();
        $user->setRoles([$rolePrefix . '_WRITER', 'ROLE_COMPANY_MANAGER']);
        $user->setModuleLevel($module, User::LEVEL_NONE);

        $roles = $user->getRoles();
        self::assertNotContains($rolePrefix . '_READER', $roles);
        self::assertNotContains($rolePrefix . '_WRITER', $roles);
        self::assertContains('ROLE_COMPANY_MANAGER', $roles, 'Les rôles hors matrice doivent être conservés');
    }

    public function testModuleLevelIsReadFromStoredRoles(): void
    {
        $user = $this->user();
        $user->setRoles(['ROLE_SITE_READER', 'ROLE_ANTENNES_WRITER']);

        self::assertSame(User::LEVEL_READER, $user->getModuleLevel('site'));
        self::assertSame(User::LEVEL_MANAGER, $user->getModuleLevel('antennes'));
        self::assertSame(User::LEVEL_NONE, $user->getModuleLevel('compta'));
    }

    public function testUnknownModuleIsIgnored(): void
    {
        $user = $this->user();
        $user->setModuleLevel('inconnu', User::LEVEL_MANAGER);

        self::assertSame([], array_values(array_diff($user->getRoles(), ['ROLE_USER'])));
    }
}
