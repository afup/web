<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use Afup\Site\Association\Personnes_Morales;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;

class StatisticsComputer
{
    private UserRepository $userRepository;
    private Personnes_Morales $personnesMorales;

    public function __construct(UserRepository $userRepository, Personnes_Morales $personnesMorales)
    {
        $this->userRepository = $userRepository;
        $this->personnesMorales = $personnesMorales;
    }

    public function computeStatistics(): Statistics
    {
        $statistics = new Statistics();
        /** @var User[] $users */
        $users = $this->userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL);
        foreach ($users as $user) {
            $statistics->usersCount++;
            if ($user->isMemberForCompany()) {
                $statistics->usersCountWithCompanies++;

                if (isset($companies[$user->getCompanyId()]) === false) {
                    $companies[$user->getCompanyId()] = true;
                    $statistics->companiesCountWithLinkedUsers++;
                }
            } else {
                $statistics->usersCountWithoutCompanies++;
            }
        }
        $statistics->companiesCount = $this->personnesMorales->obtenirNombrePersonnesMorales('1');

        return $statistics;
    }
}
