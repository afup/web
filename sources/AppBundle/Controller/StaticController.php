<?php


namespace AppBundle\Controller;

use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Utils;
use Symfony\Component\HttpFoundation\Request;

class StaticController extends SiteBaseController
{
    public function officesAction()
    {
        return $this->render(':site:offices.html.twig');
    }

    public function superAperoAction()
    {
        return $this->render(':site:superapero.html.twig');
    }

    public function superAperoLiveAction()
    {
        return $this->render(':site:superapero_live.html.twig');
    }

    public function voidAction(Request $request)
    {
        $params = [];
        if ($request->attributes->has('legacyContent')) {
            $params = $request->attributes->get('legacyContent');
        }

        return $this->render('site/base.html.twig', $params);
    }

    public function voidBackOfficeAction(Request $request)
    {
        global $smarty, $bdd, $conf;
        $params = [];
        if ($request->attributes->has('legacyContent')) {
            $params = $request->attributes->get('legacyContent');
        }
        $pages = $this->getParameter('app.pages_backoffice');

        // Gestion legacy des droits
        $droits = Utils::fabriqueDroits($bdd, $this->get('security.token_storage'));

        if (!empty($_POST['motdepasse_perdu'])) {
            /**
             * @todo ne fonctionne plus
             */
            $personnes_physiques = new Personnes_Physiques($bdd);
            $result = $personnes_physiques->envoyerMotDePasse($_POST['email']);

            if (!$result) {
                $_GET['statut'] = AFUP_CONNEXION_ERROR_LOGIN;
                $_GET['page'] = 'mot_de_passe_perdu';
            } else {
                afficherMessage('Votre mot de passe vous a été envoyé par mail', 'index.php');
            }
        }

        if (!empty($_POST['inscription'])) {
            // Initialisation de AFUP_Log
            Logs::initialiser($bdd, $droits->obtenirIdentifiant());

            /**
             * @TODO this does not work anymore
             */
            require_once 'inscription.php';
        }

        if (!empty($_GET['hash'])) {
            $droits->seDeconnecter();
            /**
             * @TODO this does not work anymore
             */
            $droits->seConnecterEnAutomatique($_GET['hash']);
        }

        /*if (!$droits->estConnecte() and $_GET['page'] != 'connexion' and $_GET['page'] != 'mot_de_passe_perdu' and
        $_GET['page'] != 'message' and $_GET['page'] != 'inscription') {
            header('Location: index.php?page=connexion&statut=' . $droits->obtenirStatutConnexion() . '&page_demandee=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }*/

        // On vérifie que l'utilisateur a le droit d'accéder à la page
        $droits->chargerToutesLesPages($pages);
        if (!$droits->verifierDroitSurLaPage($_GET['page'])) {
            afficherMessage("Vous n'avez pas le droit d'accéder à cette page", 'index.php?page=accueil', true);
        }

        // Initialisation de AFUP_Log
        Logs::initialiser($bdd, $droits->obtenirIdentifiant());

        if ($_GET['page'] == 'index' or !file_exists(dirname(__FILE__).'/../../../htdocs/pages/administration/' . $_GET['page'] . '.php')) {
            $_GET['page'] = 'accueil';
        }

        require_once dirname(__FILE__) . '/../../../htdocs/pages/administration/' . $_GET['page'] . '.php';

        // On gère des infos popups
        /*if (isset($_SESSION['flash'])) {
            $smarty->assign('flash_message', $_SESSION['flash']['message']);
            $smarty->assign('flash_erreur', $_SESSION['flash']['erreur']);
            unset($_SESSION['flash']);
        }*/

        // Affichage de la page
        $content = $smarty->fetch($_GET['page'] . '.html');

        return $this->render('admin/base_loggedin.html.twig', $params + [
                'title' => obtenirTitre($pages, $_GET['page']),
                'page' => $_GET['page'],
                'content' => $content
            ]);
    }
}
