<?php

namespace AppBundle\Association\CompanyMembership;

use AppBundle\Association\Event\UserDisabledEvent;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserCompany
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(UserRepository $userRepository, EventDispatcher $eventDispatcher)
    {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setManager(User $user)
    {
        $user->addRole('ROLE_COMPANY_MANAGER');
        $this->userRepository->save($user);
    }

    public function unsetManager(User $user)
    {
        $user->removeRole('ROLE_COMPANY_MANAGER');
        $this->userRepository->save($user);
    }

    public function disableUser(User $user)
    {
        $user
            ->removeRole('ROLE_COMPANY_MANAGER')
            ->setCompanyId(0)
            ->setStatus(User::STATUS_INACTIVE)
        ;
        $this->userRepository->save($user);

        $userDisabledEvent = new UserDisabledEvent($user);
        $this->eventDispatcher->dispatch(UserDisabledEvent::NAME, $userDisabledEvent);
    }
}
