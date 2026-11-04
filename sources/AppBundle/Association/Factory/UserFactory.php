<?php

declare(strict_types=1);

namespace AppBundle\Association\Factory;

use AppBundle\Association\Model\User;

class UserFactory
{
    public function createForRegister(): User
    {
        $user = new User();

        return
            $user
                ->setCountry('FR')
                ->setStatus(User::STATUS_ACTIVE)
        ;
    }
}
