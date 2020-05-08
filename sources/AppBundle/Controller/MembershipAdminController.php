<?php

namespace AppBundle\Controller;

use AppBundle\Association\UserMembership\StatisticsComputer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MembershipAdminController extends Controller
{
    public function reportingAction(Request $request)
    {
        // @todo Evolution nombre de personnes physiques en cours d'adhésion

        // Evolution sur 1 an:
        // Calculer le nombre au début de l'année
        // puis on fait une requete pour les nouvelles cotisations par jour & les périmées par jour

        $statistics = $this->get(StatisticsComputer::class)->computeStatistics();

        return $this->render('admin/association/membership/stats.html.twig', [
            'usersCount' => $statistics['users_count'],
            'usersCountWithoutCompanies' => $statistics['users_count_without_companies'],
            'companiesCountWithLinkedUsers' => $statistics['companies_count_with_linked_users'],
            'companiesCount' => $statistics['companies_count'],
            'usersCountWithCompanies' => $statistics['users_count_with_companies'],
            'title' => 'Reporting membres [WIP]'
        ]);
    }
}
