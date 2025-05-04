<?php

declare(strict_types=1);

namespace AppBundle\Association\Event;

use AppBundle\Association\Model\User;
use Symfony\Contracts\EventDispatcher\Event;

class NewMemberEvent extends Event
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
