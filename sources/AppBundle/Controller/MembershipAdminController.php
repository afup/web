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
        $userRepository = $this->get('app.user_repository');

        /**
         * @var $users User[]
         */
        $users = $userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL);

        $companies = [];

        $validCompanies = $validUsers = $companiesUsers = 0;
        foreach ($users as $user) {
            if ($user->isMemberForCompany()) {
                $companiesUsers++;

                if (isset($companies[$user->getCompanyId()]) === false) {
                    $companies[$user->getCompanyId()] = true;
                    $validCompanies++;
                }

            } else {
                $validUsers++;
            }
        }

        // @todo Evolution nombre de personnes physiques en cours d'adhésion

        // Evolution sur 1 an:
        // Calculer le nombre au début de l'année
        // puis on fait une requete pour les nouvelles cotisations par jour & les périmées par jour

        return $this->render('admin/association/membership/stats.html.twig', [
            'validUsers' => $validUsers,
            'validCompanies' => $validCompanies,
            'companiesUsers' => $companiesUsers,
            'title' => 'Reporting membres [WIP]'
        ]);
    }
}
