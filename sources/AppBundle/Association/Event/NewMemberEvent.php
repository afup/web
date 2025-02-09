<?php

declare(strict_types=1);

namespace AppBundle\Association\Event;

use AppBundle\Association\Model\User;
use Symfony\Component\EventDispatcher\Event;

class NewMemberEvent extends Event
{
    const NAME = 'member.new';

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
