<?php


namespace AppBundle\Controller;

use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\Utils;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\UserService;
use Exception;
use Smarty;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyController extends Controller
{
    public function voidAction()
    {
        return new Response();
    }

    public function backOfficeAction(Request $request)
    {
        /**
         * All global variables (as defined in commonStart and others) should be declared here
         */
        global $smarty, $bdd, $conf, $droits, $AFUP_Tarifs_Forum_Lib, $AFUP_Tarifs_Forum, $debug, $translator, $services;
        $droits = Utils::fabriqueDroits($bdd, $this->get('security.token_storage'), $this->get('security.authorization_checker'));
        $pages = $this->getParameter('app.pages_backoffice');

        $flashBag = $this->get('session')->getFlashBag();

        if ($_GET['page'] == 'index' or !file_exists(dirname(__FILE__) . '/../../../htdocs/pages/administration/' . $_GET['page'] . '.php')) {
            return $this->redirectToRoute('admin_home');
        }

        // On vérifie que l'utilisateur a le droit d'accéder à la page
        $droits->chargerToutesLesPages($pages);
        if (!$droits->verifierDroitSurLaPage($_GET['page'])) {
            $flashBag->add('error', "Vous n'avez pas le droit d'accéder à cette page");
            return $this->redirectToRoute('admin_home');
        }

        // Initialisation de AFUP_Log
        Logs::initialiser($bdd, $droits->obtenirIdentifiant());

        require_once dirname(__FILE__) . '/../../../htdocs/pages/administration/' . $_GET['page'] . '.php';

        // On gère des infos popups
        if (isset($_SESSION['flash']['message'])) {
            $flashBag->add('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $flashBag->add('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);

        // Récupération du contenu de la page généré par smarty
        $content = $smarty->fetch($_GET['page'] . '.html');
        $js = $smarty->fetch($_GET['page'] . '.js.html');

        return $this->render('admin/base_with_header.html.twig', [
            'title' => obtenirTitre($pages, $_GET['page']),
            'page' => $_GET['page'],
            'content' => $content,
            'js' => $js,
        ]);
    }

    public function registerAction(Request $request)
    {
        global $droits;

        $bdd = $GLOBALS['AFUP_DB'];

        $server = $_SERVER;
        $_SERVER['REQUEST_URI'] = '/administration/';
        require dirname(__FILE__) . '/../../Afup/Bootstrap/Http.php';
        /** @var Smarty $smarty */
        $userRepository = $this->get(UserRepository::class);
        /** @var UserService $userService */
        $userService = $this->get(UserService::class);
        $_SERVER = $server;
        $droits = Utils::fabriqueDroits($bdd, $this->get('security.token_storage'), $this->get('security.authorization_checker'));
        Logs::initialiser($bdd, $droits->obtenirIdentifiant());
        $pays = new Pays($bdd);

        $formulaire = instancierFormulaire();

        $formulaire->setDefaults(
            [
                'civilite' => 'M.',
                'id_pays' => 'FR',
                'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
                'niveau_apero' => AFUP_DROITS_NIVEAU_MEMBRE,
                'niveau_annuaire' => AFUP_DROITS_NIVEAU_MEMBRE,
                'etat' => AFUP_DROITS_ETAT_ACTIF,
            ]
        );

        $officesCollection = new \AppBundle\Offices\OfficesCollection();
        $offices = ['' => '-Aucune-'];
        foreach ($officesCollection->getOrderedLabelsByKey() as $key => $label) {
            $offices[$key] = $label;
        }

        $formulaire->addElement('hidden', 'inscription', 1);
        $formulaire->addElement('hidden', 'niveau');
        $formulaire->addElement('hidden', 'niveau_apero');
        $formulaire->addElement('hidden', 'niveau_annuaire');
        $formulaire->addElement('hidden', 'etat');
        $formulaire->addElement('hidden', 'compte_svn');

        $formulaire->addElement('header', '', 'Informations');
        $formulaire->addElement('select', 'civilite', 'Civilité', ['M.', 'Mme']);
        $formulaire->addElement('text', 'nom', 'Nom', ['size' => 30, 'maxlength' => 40]);
        $formulaire->addElement('text', 'prenom', 'Prénom', ['size' => 30, 'maxlength' => 40]);
        $formulaire->addElement('text', 'login', 'Login', ['size' => 30, 'maxlength' => 30]);
        $formulaire->addElement('text', 'email', 'Email', ['size' => 30, 'maxlength' => 100]);
        $formulaire->addElement('textarea', 'adresse', 'Adresse', ['cols' => 42, 'rows' => 10]);
        $formulaire->addElement('text', 'code_postal', 'Code postal', ['size' => 6, 'maxlength' => 10]);
        $formulaire->addElement('text', 'ville', 'Ville', ['size' => 30, 'maxlength' => 50]);
        $formulaire->addElement('select', 'id_pays', 'Pays', $pays->obtenirPays());
        $formulaire->addElement('text', 'telephone_fixe', 'Tél. fixe', ['size' => 20, 'maxlength' => 20]);
        $formulaire->addElement('text', 'telephone_portable', 'Tél. portable', ['size' => 20, 'maxlength' => 20]);
        $formulaire->addElement('select', 'nearest_office', 'Antenne la plus proche', $offices);

        $formulaire->addElement('password', 'mot_de_passe', 'Mot de passe', ['size' => 30, 'maxlength' => 30]);
        $formulaire->addElement('password', 'confirmation_mot_de_passe', '', ['size' => 30, 'maxlength' => 30]);
        $formulaire->addElement('header', 'boutons', '');
        $formulaire->addElement('submit', 'soumettre', 'Ajouter');

        $formulaire->addRule('nom', 'Nom manquant', 'required');
        $formulaire->addRule('prenom', 'Prénom manquant', 'required');
        $formulaire->addRule('login', 'Login manquant', 'required');
        $formulaire->addRule('login', 'Login déjà existant', 'callback', static function ($value) use ($userRepository) {
            return !$userRepository->loginExists($value);
        });
        $formulaire->addRule('email', 'Email manquant', 'required');
        $formulaire->addRule('email', 'Email invalide', 'email');
        $formulaire->addRule('adresse', 'Adresse manquante', 'required');
        $formulaire->addRule('code_postal', 'Code postal manquant', 'required');
        $formulaire->addRule('ville', 'Ville manquante', 'required');
        $formulaire->addRule('mot_de_passe', 'Mot de passe manquant', 'required');
        $formulaire->addRule(['mot_de_passe', 'confirmation_mot_de_passe'], 'Le mot de passe et sa confirmation ne concordent pas', 'compare');

        if ($formulaire->validate()) {
            try {
                $user = new User();
                $user->setStatus($formulaire->exportValue('etat'));
                $user->setUsername($formulaire->exportValue('login'));
                $user->setPlainPassword($formulaire->exportValue('mot_de_passe'));
                $user->setLevel($formulaire->exportValue('niveau'));
                $user->setDirectoryLevel($formulaire->exportValue('niveau_annuaire'));
                $user->setWebsiteLevel($formulaire->exportValue('niveau_site'));
                $user->setCivility($formulaire->exportValue('civilite'));
                $user->setLastName($formulaire->exportValue('nom'));
                $user->setFirstName($formulaire->exportValue('prenom'));
                $user->setEmail($formulaire->exportValue('email'));
                $user->setAddress($formulaire->exportValue('adresse'));
                $user->setZipCode($formulaire->exportValue('code_postal'));
                $user->setCity($formulaire->exportValue('ville'));
                $user->setCountry($formulaire->exportValue('id_pays'));
                $user->setPhone($formulaire->exportValue('telephone_fixe'));
                $user->setMobilephone($formulaire->exportValue('telephone_portable'));
                $user->setNearestOffice($formulaire->exportValue('nearest_office'));
                try {
                    $userRepository->create($user);
                    Logs::log('Ajout de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName());
                    $userService->sendWelcomeEmail($user);
                    $this->addFlash('notice', 'Merci pour votre inscription. Il ne reste plus qu\'à régler votre cotisation.');

                    return $this->get('security.authentication.guard_handler')
                        ->authenticateUserAndHandleSuccess(
                            $user,
                            $request,
                            $this->get(\AppBundle\Security\LegacyAuthenticator::class),
                            'legacy_secured_area'
                        );
                } catch (Exception $e) {
                    $smarty->assign('erreur', 'Une erreur est survenue lors de la création de votre compte. Veuillez recommencer. Merci.');
                }
            } catch (Exception $e) {
                $message = sprintf('Une erreur est survenue lors de la création de votre compte (%s). N\'hésitez pas à contacter le bureau via bonjour@afup.org si vous ne comprenez pas l\'erreur en nous précisant le message qui vous est donné. Merci !', $e->getMessage());
                $smarty->assign('erreur', $message);
            }
        }

        $smarty->assign('formulaire', genererFormulaire($formulaire));

        return $this->render('admin/base_with_header.html.twig', [
            'title' => 'Inscription',
            'page' => 'inscription',
            'content' => $smarty->fetch(dirname(__FILE__) . '/../../../htdocs/templates/administration/inscription.html')
        ]);
    }
}
