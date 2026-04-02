<?php

declare(strict_types=1);

namespace AppBundle\Security;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Psr\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener]
final readonly class LoginSuccessListener
{
    public function __construct(
        private UserRepository $userRepository,
        private ClockInterface $clock,
    ) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $user->setLastLogin($this->clock->now());
        $this->userRepository->save($user);
    }
}
