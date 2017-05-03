<?php

namespace AppBundle\Association\Event;

use AppBundle\Association\Model\User;
use Symfony\Component\EventDispatcher\Event;

class UserDisabledEvent extends Event
{
    const NAME = 'user.disabled';

    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
