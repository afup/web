<?php

declare(strict_types=1);

namespace AppBundle\Association\CompanyMembership;

use AppBundle\Association\Event\UserDisabledEvent;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserCompany
{
    private UserRepository $userRepository;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(UserRepository $userRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setManager(User $user): void
    {
        $user->addRole('ROLE_COMPANY_MANAGER');
        $this->userRepository->save($user);
    }

    public function unsetManager(User $user): void
    {
        $user->removeRole('ROLE_COMPANY_MANAGER');
        $this->userRepository->save($user);
    }

    public function disableUser(User $user): void
    {
        $user
            ->removeRole('ROLE_COMPANY_MANAGER')
            ->setCompanyId(0)
            ->setStatus(User::STATUS_INACTIVE)
        ;
        $this->userRepository->save($user);

        $event = new UserDisabledEvent($user);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }
}
