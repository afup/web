<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Association\Model\User;
use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Entity\Repository\GithubUserRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final class GithubUserRepositoryTest extends IntegrationTestCase
{
    public function testGetAllOrderedByLoginIsCaseInsensitive(): void
    {
        $repository = self::getContainer()->get(GithubUserRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertGithubUser($connection, 1, 'bob');
        $this->insertGithubUser($connection, 2, 'Alice');
        $this->insertGithubUser($connection, 3, 'charlie');

        $logins = array_map(
            static fn(GithubUser $user): string => $user->getLogin(),
            $repository->getAllOrderedByLogin(),
        );

        self::assertSame(['Alice', 'bob', 'charlie'], $logins);
    }

    public function testFindOneByGithubId(): void
    {
        $repository = self::getContainer()->get(GithubUserRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertGithubUser($connection, 4242, 'octocat', 'The Octocat', true);

        $user = $repository->findOneByGithubId(4242);
        self::assertInstanceOf(GithubUser::class, $user);
        self::assertSame('octocat', $user->getLogin());
        self::assertSame('The Octocat', $user->getName());
        self::assertSame('https://github.com/octocat', $user->getProfileUrl());
        self::assertSame('https://avatars.githubusercontent.com/u/4242', $user->getAvatarUrl());
        self::assertTrue($user->getAfupCrew());

        self::assertNull($repository->findOneByGithubId(9999));
    }

    public function testSave(): void
    {
        $repository = self::getContainer()->get(GithubUserRepository::class);

        $user = GithubUser::fromApi([
            'login' => 'octocat',
            'id' => 583231,
            'avatar_url' => 'https://avatars.githubusercontent.com/u/583231',
            'company' => '@github',
            'name' => null,
            'html_url' => 'https://github.com/octocat',
        ]);
        $repository->save($user);

        self::assertNotNull($user->getId());
        self::getContainer()->get('doctrine.orm.entity_manager')->clear();

        $saved = $repository->find($user->getId());
        self::assertInstanceOf(GithubUser::class, $saved);
        self::assertSame(583231, $saved->getGithubId());
        self::assertSame('@github', $saved->getCompany());
        self::assertNull($saved->getName());
        self::assertFalse($saved->getAfupCrew());
    }

    public function testLoadUserByIdentifier(): void
    {
        $repository = self::getContainer()->get(GithubUserRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertGithubUser($connection, 4242, 'octocat');

        self::assertSame(4242, $repository->loadUserByIdentifier('octocat')->getGithubId());

        $this->expectException(UserNotFoundException::class);
        $repository->loadUserByIdentifier('unknown');
    }

    public function testRefreshUserReloadsFromSerializedUser(): void
    {
        $repository = self::getContainer()->get(GithubUserRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertGithubUser($connection, 4242, 'octocat', 'The Octocat', true);
        $user = $repository->loadUserByIdentifier('octocat');

        /** @var GithubUser $unserialized */
        $unserialized = unserialize(serialize($user));
        self::assertSame($user->getId(), $unserialized->getId());
        self::assertNull($unserialized->getName());

        self::getContainer()->get('doctrine.orm.entity_manager')->clear();

        $refreshed = $repository->refreshUser($unserialized);
        self::assertSame($user->getId(), $refreshed->getId());
        self::assertSame('The Octocat', $refreshed->getName());
        self::assertContains('ROLE_AFUP_CREW', $refreshed->getRoles());
        self::assertTrue($refreshed->isEqualTo($unserialized));
    }

    public function testRefreshUserThrowsWhenUserNoLongerExists(): void
    {
        $repository = self::getContainer()->get(GithubUserRepository::class);

        $this->expectException(UserNotFoundException::class);
        $repository->refreshUser(new GithubUser()->setId(12345)->setLogin('ghost'));
    }

    public function testRefreshUserRejectsOtherUserClasses(): void
    {
        $repository = self::getContainer()->get(GithubUserRepository::class);

        $this->expectException(UnsupportedUserException::class);
        $repository->refreshUser(new User());
    }

    public function testSupportsClass(): void
    {
        $repository = self::getContainer()->get(GithubUserRepository::class);

        self::assertTrue($repository->supportsClass(GithubUser::class));
        self::assertFalse($repository->supportsClass(User::class));
    }

    private function insertGithubUser(Connection $connection, int $githubId, string $login, ?string $name = null, bool $afupCrew = false): void
    {
        $connection->insert('afup_user_github', [
            'github_id' => $githubId,
            'login' => $login,
            'name' => $name,
            'company' => null,
            'profile_url' => 'https://github.com/' . $login,
            'avatar_url' => 'https://avatars.githubusercontent.com/u/' . $githubId,
            'afup_crew' => $afupCrew ? 1 : 0,
        ]);
    }
}
