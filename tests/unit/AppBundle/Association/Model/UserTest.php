<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association\Model;

use AppBundle\Association\Model\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public static function provideModules(): iterable
    {
        foreach (['apero', 'membres', 'site', 'event', 'antennes', 'veille', 'compta', 'ag', 'planete'] as $module) {
            yield $module => [$module];
        }
    }

    public static function provideModuleRoles(): iterable
    {
        yield 'apero' => ['apero', 'ROLE_APERO'];
        yield 'membres' => ['membres', 'ROLE_MEMBRES'];
        yield 'site' => ['site', 'ROLE_SITE'];
        yield 'event' => ['event', 'ROLE_EVENT'];
        yield 'antennes' => ['antennes', 'ROLE_ANTENNES'];
        yield 'veille' => ['veille', 'ROLE_VEILLE'];
        yield 'compta' => ['compta', 'ROLE_COMPTA'];
        yield 'ag' => ['ag', 'ROLE_AG'];
        yield 'planete' => ['planete', 'ROLE_PLANETE'];
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

    #[DataProvider('provideModules')]
    public function testNewUserHasNoAccess(string $module): void
    {
        $user = $this->user();

        self::assertSame(0, $user->getModuleLevel($module));
        self::assertFalse($user->isAdminAccess());
        self::assertFalse($user->canAccessAdmin());
        self::assertFalse(in_array('ROLE_ADMIN', $user->getRoles(), true));
    }

    #[DataProvider('provideModuleRoles')]
    public function testReaderLevelGrantsReaderRole(string $module, string $legacyPrefix): void
    {
        $user = $this->user();
        $user->setModuleLevel($module, User::LEVEL_READER);

        $roles = $user->getRoles();
        self::assertContains($legacyPrefix . '_READER', $roles);
        self::assertNotContains($legacyPrefix . '_WRITER', $roles);
        self::assertTrue($user->canAccessAdmin(), 'Un accès lecture seule doit donner accès à l’admin');
    }

    #[DataProvider('provideModuleRoles')]
    public function testManagerLevelGrantsWriterRole(string $module, string $legacyPrefix): void
    {
        $user = $this->user();
        $user->setModuleLevel($module, User::LEVEL_ADMIN);

        $roles = $user->getRoles();
        self::assertContains($legacyPrefix . '_WRITER', $roles);
        self::assertNotContains($legacyPrefix . '_READER', $roles, 'Le WRITER suffit via la hiérarchie WRITE ⇒ READ');
        self::assertTrue($user->canAccessAdmin());
    }

    public function testLegacyRolesStoredInDbAreStripped(): void
    {
        $user = $this->user();
        $user->setRoles(['ROLE_APERO', 'ROLE_FORUM', 'ROLE_EVENT_WRITER', 'ROLE_COMPANY_MANAGER']);
        $user->setModuleLevel('apero', User::LEVEL_MEMBER);

        $roles = $user->getRoles();
        self::assertNotContains('ROLE_APERO', $roles, 'Le rôle historique doit être recalculé depuis les niveau-modules');
        self::assertNotContains('ROLE_EVENT_WRITER', $roles, 'Le rôle de la matrice en base doit être recalculé depuis les niveau-modules');
        self::assertContains('ROLE_COMPANY_MANAGER', $roles, 'Les rôles hors matrice doivent être conservés');
    }

    public function testAdminAccessCheckboxGrantsConsoleMarkerOnly(): void
    {
        $user = $this->user();
        $user->setAdminAccess(true);

        $roles = $user->getRoles();
        self::assertContains('ROLE_ADMIN', $roles);
        self::assertTrue($user->canAccessAdmin());

        $user->setAdminAccess(false);
        self::assertNotContains('ROLE_ADMIN', $user->getRoles());
        self::assertFalse($user->canAccessAdmin(), 'Un compte sans aucun rôle fonctionnel n’a plus accès à l’admin');
    }

    public function testLegacyLevelModulesArePaddedAndKept(): void
    {
        $user = $this->user();
        $user->setLevelModules('00202');

        self::assertSame('002020000', $user->getLevelModules());
        self::assertTrue($user->canAccessAdmin());
        self::assertNotContains('ROLE_SITE_READER', $user->getRoles());
    }

    public function testLegacyAccessorsMapToModuleIndexes(): void
    {
        $user = $this->user();
        $user->setWebsiteLevel(User::LEVEL_READER);
        $user->setOfficeLevel(User::LEVEL_ADMIN);

        self::assertSame(User::LEVEL_READER, $user->getModuleLevel('site'));
        self::assertSame(User::LEVEL_ADMIN, $user->getModuleLevel('antennes'));
        self::assertSame('001020000', $user->getLevelModules());
    }
}
