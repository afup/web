<?php

namespace AppBundle\Controller;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MembershipAdminController extends Controller
{
    public function reportingAction(Request $request)
    {
        $userRepository = $this->get(\AppBundle\Association\Model\Repository\UserRepository::class);

        /**
         * @var $users User[]
         */
        $users = $userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL);

        $companies = [];

        $companiesCount = $usersCountWithoutCompanies = $usersCountWithCompanies = 0;
        foreach ($users as $user) {
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

        // @todo Evolution nombre de personnes physiques en cours d'adhésion

        // Evolution sur 1 an:
        // Calculer le nombre au début de l'année
        // puis on fait une requete pour les nouvelles cotisations par jour & les périmées par jour

        return $this->render('admin/association/membership/stats.html.twig', [
            'usersCountWithoutCompanies' => $usersCountWithoutCompanies,
            'companiesCount' => $companiesCount,
            'usersCountWithCompanies' => $usersCountWithCompanies,
            'title' => 'Reporting membres [WIP]'
        ]);
    }
}
