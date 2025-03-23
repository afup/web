<?php

declare(strict_types=1);

namespace AppBundle\Association\Event;

use AppBundle\Association\Model\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserDisabledEvent extends Event
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
