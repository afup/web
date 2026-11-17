<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Entity;

use AppBundle\Association\Model\User;
use AppBundle\Event\Entity\GithubUser;
use PHPUnit\Framework\TestCase;

final class GithubUserTest extends TestCase
{
    public function testGetRoles(): void
    {
        $user = new GithubUser();
        self::assertSame(['ROLE_GITHUB'], $user->getRoles());

        $user->setAfupCrew(true);
        self::assertSame(['ROLE_GITHUB', 'ROLE_AFUP_CREW'], $user->getRoles());
    }

    public function testIsEqualToComparesIds(): void
    {
        $user = new GithubUser()->setId(1)->setLogin('octocat');

        self::assertTrue($user->isEqualTo(new GithubUser()->setId(1)->setLogin('renamed')));
        self::assertFalse($user->isEqualTo(new GithubUser()->setId(2)->setLogin('octocat')));
        self::assertFalse($user->isEqualTo(new User()));
    }

    public function testSerializationKeepsOnlyIdAndLogin(): void
    {
        $user = new GithubUser()
            ->setId(1)
            ->setLogin('octocat')
            ->setName('The Octocat')
            ->setAfupCrew(true);

        $unserialized = unserialize(serialize($user));

        self::assertInstanceOf(GithubUser::class, $unserialized);
        self::assertSame(1, $unserialized->getId());
        self::assertSame('octocat', $unserialized->getUserIdentifier());
        self::assertNull($unserialized->getName());
        self::assertFalse($unserialized->getAfupCrew());
    }

    public function testLabel(): void
    {
        $user = new GithubUser()->setLogin('octocat');
        self::assertSame('octocat', (string) $user);

        $user->setName('The Octocat');
        self::assertSame('octocat (The Octocat)', $user->getLabel());
    }
}
