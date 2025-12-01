<?php

declare(strict_types=1);

namespace AppBundle\Security;

use AppBundle\Association\Model\User;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Security\Exception\UnexpectedUserTypeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class Authentication
{
    public function __construct(
        private Security $security,
    ) {}

    public function getAfupUser(): User
    {
        return $this->getTypedUserOrThrow(User::class, 'afup');
    }

    public function getAfupUserOrNull(): ?User
    {
        return $this->getTypedUser(User::class, 'afup');
    }

    public function getGithubUser(): GithubUser
    {
        return $this->getTypedUserOrThrow(GithubUser::class, 'github');
    }

    public function getGithubUserOrNull(): ?GithubUser
    {
        return $this->getTypedUser(GithubUser::class, 'github');
    }

    /**
     * @template T of UserInterface
     * @param class-string<T> $userClass
     * @param string $expectedType
     * @return ?T
     */
    private function getTypedUser(string $userClass, string $expectedType): ?UserInterface
    {
        $user = $this->security->getUser();

        if ($user !== null && !$user instanceof $userClass) {
            throw new UnexpectedUserTypeException($expectedType);
        }

        return $user;
    }

    /**
     * @template T of UserInterface
     * @param class-string<T> $userClass
     * @param string $expectedType
     * @return T
     */
    private function getTypedUserOrThrow(string $userClass, string $expectedType): UserInterface
    {
        $user = $this->getTypedUser($userClass, $expectedType);

        if ($user === null) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}
