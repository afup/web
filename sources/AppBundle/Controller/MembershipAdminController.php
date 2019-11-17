<?php

namespace AppBundle\Controller;

use Afup\Site\Association\Personnes_Morales;
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

        $personnes_morales = new Personnes_Morales($GLOBALS['bdd']);

        // @todo Evolution nombre de personnes physiques en cours d'adhésion

        // Evolution sur 1 an:
        // Calculer le nombre au début de l'année
        // puis on fait une requete pour les nouvelles cotisations par jour & les périmées par jour

        return $this->render('admin/association/membership/stats.html.twig', [
            'usersCount' => $usersCount,
            'usersCountWithoutCompanies' => $usersCountWithoutCompanies,
            'companiesCountWithLinkedUsers' => $companiesCount,
            'companiesCount' => $personnes_morales->obtenirNombrePersonnesMorales('1'),
            'usersCountWithCompanies' => $usersCountWithCompanies,
            'title' => 'Reporting membres [WIP]'
        ]);
    }
}
