<?php

declare(strict_types=1);

namespace AppBundle\Association\CompanyMembership;

use AppBundle\Association\Event\UserDisabledEvent;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserCompany
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
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

        $this->eventDispatcher->dispatch(new UserDisabledEvent($user));
    }
}
