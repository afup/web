<?php

declare(strict_types=1);

namespace AppBundle\Controller\MembershipAdmin;

use AppBundle\Association\UserMembership\StatisticsComputer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ReportingAction
{
    public function __construct(
        private readonly StatisticsComputer $statisticsComputer,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $statistics = $this->statisticsComputer->computeStatistics();
        // @todo Evolution nombre de personnes physiques en cours d'adhésion
        // Evolution sur 1 an:
        // Calculer le nombre au début de l'année
        // puis on fait une requete pour les nouvelles cotisations par jour & les périmées par jour

        return new Response($this->twig->render('admin/association/membership/stats.html.twig', [
            'usersCount' => $statistics->usersCount,
            'usersCountWithoutCompanies' => $statistics->usersCountWithoutCompanies,
            'companiesCountWithLinkedUsers' => $statistics->companiesCountWithLinkedUsers,
            'companiesCount' => $statistics->companiesCount,
            'usersCountWithCompanies' => $statistics->usersCountWithCompanies,
            'title' => 'Reporting membres [WIP]',
        ]));
    }
}
