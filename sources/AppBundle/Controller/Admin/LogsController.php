<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin;

use Afup\Site\Corporate\_Site_Base_De_Donnees;
use Afup\Site\Utils\Logs;
use AppBundle\Association\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LogsController extends AbstractController
{
    public function __construct() {}

    public function __invoke(#[CurrentUser] UserInterface $user, int $page): Response
    {
        $bdd = new _Site_Base_De_Donnees();
        Logs::initialiser($bdd, $user instanceof User ? $user->getId() : 0);
        ;

        return $this->render('admin/logs.html.twig', [
            'logs' => Logs::obtenirTous($page),
            'nbPages' => Logs::obtenirNombrePages(),
            'currentPage' => $page,
        ]);
    }
}
