<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use Afup\Site\Corporate\_Site_Base_De_Donnees;
use AppBundle\Event\Model\Repository\EventRepository;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

class HealthcheckController extends AbstractController
{
    public function __construct(private readonly RepositoryFactory $ting)
    {
    }

    public function __invoke(): Response
    {
        $php = new DateTime();

        $bdd = new _Site_Base_De_Donnees();
        $mysqlBdd = $bdd->obtenirUn('SELECT CURRENT_TIMESTAMP');
        $mysqlBdd = new DateTime($mysqlBdd);

        $repo = $this->ting->get(EventRepository::class);
        $mysqlTing = $repo->getQuery('SELECT CURRENT_TIMESTAMP')->execute()['CURRENT_TIMESTAMP'];
        $mysqlTing = new DateTime($mysqlTing);

        $diff = $php->getTimestamp() !== $mysqlBdd->getTimestamp() || $php->getTimestamp() !== $mysqlTing->getTimestamp();

        return $this->render('admin/healthcheck.html.twig', [
            'dates' => [
                'php' => $php->format(\DateTime::ATOM),
                'mysql_bdd' => $mysqlBdd->format(\DateTime::ATOM),
                'mysql_ting' => $mysqlTing->format(\DateTime::ATOM),
                'diff' => $diff,
            ],
            'versions' => [
                'php' => phpversion(),
                'symfony' => Kernel::VERSION,
            ],
        ]);
    }
}
