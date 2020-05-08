<?php

namespace AppBundle\Association\UserMembership;

use Afup\Site\Association\Personnes_Morales;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;

class StatisticsComputer
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Personnes_Morales
     */
    private $personnesMorales;

    public function __construct(UserRepository $userRepository, Personnes_Morales $personnesMorales)
    {
        $this->userRepository = $userRepository;
        $this->personnesMorales = $personnesMorales;
    }

    public function computeStatistics()
    {
        /**
         * @var $users User[]
         */
        $users = $this->userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL);

        $companiesCount = $usersCountWithoutCompanies = $usersCountWithCompanies = $usersCount = 0;
        foreach ($users as $user) {
            $usersCount++;
            if ($user->isMemberForCompany()) {
                $usersCountWithCompanies++;

                if (isset($companies[$user->getCompanyId()]) === false) {
                    $companies[$user->getCompanyId()] = true;
                    $companiesCount++;
                }
            } else {
                $usersCountWithoutCompanies++;
            }
        }

        return [
            'users_count' => $usersCount,
            'users_count_without_companies' => $usersCountWithoutCompanies,
            'companies_count_with_linked_users' => $companiesCount,
            'companies_count' => $this->personnesMorales->obtenirNombrePersonnesMorales('1'),
            'users_count_with_companies' => $usersCountWithCompanies,
        ];
    }
}
