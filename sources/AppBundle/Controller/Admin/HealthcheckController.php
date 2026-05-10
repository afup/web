<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use AppBundle\Event\Model\Repository\EventRepository;
use DateTime;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

class HealthcheckController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly Connection $dbalConnection,
    ) {}

    public function __invoke(): Response
    {
        $php = new DateTime();

        $mysqlBdd = $this->dbalConnection->executeQuery('SELECT CURRENT_TIMESTAMP')->fetchOne();
        $mysqlBdd = new DateTime($mysqlBdd);

        $mysqlTing = $this->eventRepository->getQuery('SELECT CURRENT_TIMESTAMP')->execute()['CURRENT_TIMESTAMP'];
        $mysqlTing = new DateTime($mysqlTing);

        $diff = $php->getTimestamp() !== $mysqlBdd->getTimestamp() || $php->getTimestamp() !== $mysqlTing->getTimestamp();

        return $this->render('admin/healthcheck.html.twig', [
            'dates' => [
                'php' => $php->format(\DateTime::ATOM),
                'mysql_dbal' => $mysqlBdd->format(\DateTime::ATOM),
                'mysql_ting' => $mysqlTing->format(\DateTime::ATOM),
                'diff' => $diff,
            ],
            'versions' => [
                'php' => phpversion(),
                'symfony' => Kernel::VERSION,
            ],
            'deployment' => [
                'commit' => getenv('CC_COMMIT_ID'),
            ],
        ]);
    }
}
