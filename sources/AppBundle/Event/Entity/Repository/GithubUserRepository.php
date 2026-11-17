<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\GithubUser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @extends EntityRepository<GithubUser>
 * @implements UserProviderInterface<GithubUser>
 */
final class GithubUserRepository extends EntityRepository implements UserProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GithubUser::class);
    }

    public function findOneByGithubId(int $githubId): ?GithubUser
    {
        return $this->findOneBy(['githubId' => $githubId]);
    }

    /**
     * Le tri est insensible à la casse : la colonne login est en utf8mb4_bin.
     *
     * @return list<GithubUser>
     */
    public function getAllOrderedByLogin(): array
    {
        /** @var list<GithubUser> $users */
        $users = $this->createQueryBuilder('u')
            ->orderBy('LOWER(u.login)', 'ASC')
            ->addOrderBy('u.login', 'ASC')
            ->getQuery()
            ->getResult();

        return $users;
    }

    public function loadUserByIdentifier(string $identifier): GithubUser
    {
        $user = $this->findOneBy(['login' => $identifier]);
        if ($user === null) {
            $exception = new UserNotFoundException();
            $exception->setUserIdentifier($identifier);

            throw $exception;
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): GithubUser
    {
        if (!$user instanceof GithubUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $refreshedUser = $user->getId() !== null ? $this->find($user->getId()) : null;
        if ($refreshedUser === null) {
            $exception = new UserNotFoundException();
            $exception->setUserIdentifier($user->getUserIdentifier());

            throw $exception;
        }

        return $refreshedUser;
    }

    public function supportsClass(string $class): bool
    {
        return $class === GithubUser::class || is_subclass_of($class, GithubUser::class);
    }
}
