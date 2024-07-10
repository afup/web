<?php

namespace AppBundle\Association\Factory;

use Afup\Site\Droits;
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
                ->setLevel(Droits::AFUP_DROITS_NIVEAU_MEMBRE)
                ->setStatus(Droits::AFUP_DROITS_ETAT_ACTIF)
                ->setDirectoryLevel(Droits::AFUP_DROITS_NIVEAU_MEMBRE)
        ;
    }
}
