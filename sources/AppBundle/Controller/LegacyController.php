<?php

declare(strict_types=1);


namespace AppBundle\Controller;

use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class LegacyController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;
    private AuthorizationCheckerInterface $authorizationChecker;
    private RequestStack $requestStack;
    private array $backOfficePages;

    public function __construct(TokenStorageInterface $tokenStorage,
                                AuthorizationCheckerInterface $authorizationChecker,
                                RequestStack $requestStack,
                                array $backOfficePages)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;
        $this->backOfficePages = $backOfficePages;
    }
    public function void()
    {
        return new Response();
    }

    public function backOffice()
    {
        /**
         * All global variables (as defined in commonStart and others) should be declared here
         */
        global $smarty, $bdd, $conf, $droits, $AFUP_Tarifs_Forum_Lib, $AFUP_Tarifs_Forum, $debug, $translator, $services;
        $droits = Utils::fabriqueDroits($this->tokenStorage, $this->authorizationChecker);
        $pages = $this->backOfficePages;
        /** @var Session $session */
        $session = $this->requestStack->getSession();
        if ($_GET['page'] == 'index' || !file_exists(__DIR__ . '/../../../htdocs/pages/administration/' . $_GET['page'] . '.php')) {
            return $this->redirectToRoute('admin_home');
        }
        // On vérifie que l'utilisateur a le droit d'accéder à la page
        $droits->chargerToutesLesPages($pages);
        if (!$droits->verifierDroitSurLaPage($_GET['page'])) {
            $session->getFlashBag()->add('error', "Vous n'avez pas le droit d'accéder à\u{a0}cette page");
            return $this->redirectToRoute('admin_home');
        }
        // Initialisation de AFUP_Log
        Logs::initialiser($bdd, $droits->obtenirIdentifiant());
        require_once __DIR__ . '/../../../htdocs/pages/administration/' . $_GET['page'] . '.php';
        // On gère des infos popups
        if (isset($_SESSION['flash']['message'])) {
            $session->getFlashBag()->add('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $session->getFlashBag()->add('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);
        // Récupération du contenu de la page généré par smarty
        $content = $smarty->fetch($_GET['page'] . '.html');

        $file = $_GET['page'] . '.js.html';
        $js = '';
        if ($smarty->templateExists($file)) {
            $js = $smarty->fetch($file);
        }

        return $this->render('admin/base_with_header.html.twig', [
            'title' => obtenirTitre($pages, $_GET['page']),
            'page' => $_GET['page'],
            'content' => $content,
            'js' => $js,
        ]);
    }
}
