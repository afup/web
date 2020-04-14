<?php

namespace AppBundle\Controller\Admin;

use Afup\Site\Droits;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Utils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig_Environment;

class BackOfficeLegacyBridge
{
    /** @var SessionInterface */
    private $session;
    /** @var Droits */
    private $droits;
    /** @var array */
    private $pages;
    /** @var Twig_Environment */
    private $twig;
    private $initialized = false;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        SessionInterface $session,
        Twig_Environment $twig,
        array $pages
    ) {
        $this->session = $session;
        $this->pages = $pages;
        $this->twig = $twig;
        global $bdd;
        $this->droits = Utils::fabriqueDroits($bdd, $tokenStorage, $authorizationChecker);
    }

    /**
     * @return RedirectResponse|null
     */
    public function handlePage($page)
    {
        $this->initialize();
        // On vérifie que l'utilisateur a le droit d'accéder à la page
        if (!$this->checkPermissions($page)) {
            if ($this->session instanceof Session) {
                $this->session->getFlashBag()->add('error', 'Vous n\'avez pas le droit d\'accéder à cette page');
            }

            return new RedirectResponse('/pages/administration/index.php?page=accueil');
        }

        return null;
    }

    public function render($page, $content, $js = '')
    {
        return new Response($this->twig->render('admin/base_with_header.html.twig', [
            'title' => $this->getTitle($page),
            'page' => $page,
            'content' => $content,
            'js' => $js,
        ]));
    }

    public function log($message)
    {
        Logs::log($message);
    }

    /**
     * @param string $message
     * @param string $url
     * @param bool   $erreur
     *
     * @return RedirectResponse
     */
    public function afficherMessage($message, $url, $erreur = false)
    {
        $_SESSION['flash']['message'] = $message;
        $_SESSION['flash']['erreur'] = $erreur;

        return new RedirectResponse($url);
    }

    private function initialize()
    {
        if ($this->initialized) {
            return;
        }
        $this->droits->chargerToutesLesPages($this->pages);
        // Initialisation de AFUP_Log
        Logs::initialiser($bdd, $this->droits->obtenirIdentifiant());
        // Récupération des flash messages du legacy
        if ($this->session instanceof Session) {
            $flashBag = $this->session->getFlashBag();
            if (isset($_SESSION['flash']['message'])) {
                $flashBag->add('notice', $_SESSION['flash']['message']);
            }
            if (isset($_SESSION['flash']['erreur'])) {
                $flashBag->add('error', $_SESSION['flash']['erreur']);
            }
            unset($_SESSION['flash']);
        }
        $this->initialized = true;
    }

    public function getTitle($page)
    {
        return obtenirTitre($this->pages, $page);
    }

    private function checkPermissions($page)
    {
        return $this->droits->verifierDroitSurLaPage($page);
    }
}
