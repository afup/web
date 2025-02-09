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
                ->setCivility(User::CIVILITE_M)
                ->setCountry('FR')
                ->setLevel(User::LEVEL_MEMBER)
                ->setStatus(User::STATUS_ACTIVE)
                ->setDirectoryLevel(User::LEVEL_MEMBER)
        ;
    }
}
