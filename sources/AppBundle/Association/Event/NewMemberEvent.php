<?php

namespace AppBundle\Association\Event;

use AppBundle\Association\Model\User;
use Symfony\Component\EventDispatcher\Event;

class NewMemberEvent extends Event
{
    const NAME = 'member.new';

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
