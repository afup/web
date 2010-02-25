<?php

/*function mail($to, $subject, $message, $headers)
{
echo "<pre>TO   : " . $to . "<br />\n";
echo "SUBJ : " . $subject . "<br />\n";
echo "MESS : " . $message . "<br />\n";
echo "HEAD : " . $headers . "<br />\n</pre>";
}*/

/**
 * Controleur frontal de l'annuaire. 
 * 
 * @todo remanier cette classe dans les controleurs de l'implémentation MVC lors du remaniement vers PHP 5 / Zend Framework
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Association Française des Utilisateurs de PHP
 * @since 1.0 - Fri Jun 02 20:35:00 CEST 2006
 * @package afup
 * @subpackage directory
 */
class Afup_Directory_Controller
{

    /**
     * Fichier contenant les message en instance d'envoi. (chemin UNIX)
     */
    const MESSAGES_FILE = '/tmp/afup_directory_messages.tmp';

    private $dsn        = null;
    private $user       = null;
    private $pass       = null;
    private $dbEncoding = null;

    /**
     * PDO instance
     *
     * @var PDO
     */
    private $pdo = null;

    /**
     * Instance de la classe (singleton)
     *
     * @var Afup_Directory_Controller
     */
    private static $instance = null;

    /**
     * Instance du moteur de template. 
     *
     * @var Afup_Directory_Template
     */
    protected $template = null;

    /**
     * Chargement du moteur de templates et du contexte du controleur. 
     */
    private function __construct($dsn, $user, $pass, $dbEncoding)
    {
        $this->dsn  = $dsn;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbEncoding =  $dbEncoding;
        Afup::includeClass('Afup_Directory_Template');
        $this->template = new Afup_Directory_Template();
        $appUrl = 'http://' . $_SERVER['HTTP_HOST'] . preg_replace('/^(.*\/)[^\/]*$/', '$1', $_SERVER['REQUEST_URI']);
        $this->template->assign('application_url', $appUrl);
        $this->pdo = new PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($dbEncoding == 'utf8') {
            $this->pdo->exec('SET CHARACTER SET ' . $dbEncoding);
        }
    }

    /**
     * Renvoit l'instance du singleton. 
     *
     * @return Afup_Directory_Controller
     */
    public static function getInstance($dsn, $user = '', $pass = '', $dbEncoding = 'utf8')
    {
        if (self::$instance === null) {
            self::$instance = new Afup_Directory_Controller($dsn, $user, $pass, $dbEncoding);
        }
        return self::$instance;
    }

    /**
     * Retourne une instance du gestionnaire d'annuaire
     *
     * @return Afup_Directory
     */
    private function getAfupDirectory()
    {
        return Afup_Directory::getInstance($this->pdo);
    }

    /**
     * Gestion de la navigation (dispatcher du contrôleur)
     *
     * @return boolean
     */
    public function dispatch()
    {
        try {
            $action = isset($_GET['action']) ? $_GET['action'] : 'list';
            $actionMethod = $action . 'Action';
            $validationMethod = $action . 'Validator';
            $errors = array();
            if (method_exists($this, $validationMethod)) {
                $errors = $this->$validationMethod($_GET, $_POST);
                $this->template->assign('errors', $errors);
            }
            if (method_exists($this, $actionMethod)) {
                $this->$actionMethod($_GET, $_POST, $errors);
            } else {
                trigger_error('Unknow action "' . $action . '"', E_USER_WARNING);
                return false;
            }
            $this->template->display($action . '.html');
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $this->template->assign('error', "Il existe déjà une donnée similaire à celle que vous souhaitez valider.");
            } else {
                $this->template->assign('error', "Problème d'accès à la base de données.");
            }
            $this->template->assign('exception', $e);
        } catch (Afup_Directory_Exception $e) {
            var_dump($e);
            $this->template->assign('error', "Un problème interne à l'application annuaire ne nous permet pas de continuer.");
            $this->template->assign('exception', $e);
            //AFUP_Logs::_obtenirInstance()->log("Erreur dans l'annuaire : " . $e->getMessage());
        } catch (Afup_Exception $e) {
            $this->template->assign('error', "Un problème interne aux librairies AFUP ne nous permet pas de continuer.");
            $this->template->assign('exception', $e);
        } catch (Exception $e) {
            $this->template->assign('error', "Un problème interne ne nous permet pas de continuer.");
            $this->template->assign('exception', $e);
        }
        $this->template->display('error.html');
        return false;
    }

    // Validators

    public function listValidator($get, $post)
    {
        $errors = array();
        if (isset($get['page']) && (!is_numeric($get['page']) || $get['page'] < 0)) {
            $errors[] = 'Paramètres incorrects.';
        }
        return $errors;
    }

    // Actions

    /**
     * Affichage de la liste des entreprises. 
     *
     * @throws Afup_Directory_Exception
     * @param array $get
     * @param array $post
     * @param array $errors
     * @return boolean
     */
    public function listAction($get, $post, $errors)
    {
        if (count($errors)) {
            return false;
        }
        Afup::includeClass('Afup_Directory');
        Afup::includeClass('Afup_Directory_Config');
        Afup::includeClass('Afup_Directory_Tools');
        $afupDirectory = $this->getAfupDirectory();
        $criteria = array('Valide' => '1');
        if (isset($_GET['params']) && is_array($_GET['params'])) {
            foreach ($_GET['params'] as $key => $param) {
                if ($param === '-1') {
                    continue;
                }
                switch ($key) {
                    case 'Activite' :
                    case 'Zone' :
                    case 'FormeJuridique' :
                    case 'TailleSociete' :
                        if (is_numeric($param) && $param >= 0) {
                            $criteria[$key] = (integer) $param;
                            break;
                        }
                        throw new Afup_Directory_Exception("Incorrect parameter value");
                    default :
                        throw new Afup_Directory_Exception("Incorrect parameter key");
                }
            }
        }
        $params  = array('criteria' => $criteria);
        $get['page'] = isset($get['page']) ? $get['page'] : '';
        $list    =  $afupDirectory->getCompanies((int) $get['page'], Afup_Directory_Config::getLinesByPage(), $params);
        $nbPages = ceil($afupDirectory->countCompanies($params) / Afup_Directory_Config::getLinesByPage());

        $pages   = Afup_Directory_Tools::getPaginationTable($nbPages, (int) $get['page']);
        $activities  = array(-1 => "-- Toute activité --")        + Afup_Directory_Config::getActivities();
        $zones       = array(-1 => "-- Toute zone --")            + Afup_Directory_Config::getZone();
        $legalstatus = array(-1 => "-- Toute forme juridique --") + Afup_Directory_Config::getLegalStatus();
        $sizes       = array(-1 => "-- Toute taille --")          + Afup_Directory_Config::getCompanySize();
        $this->template->assign('list',        $list);
        $this->template->assign('pages',       $pages);
        $this->template->assign('activities',  $activities);
        $this->template->assign('zones',       $zones);
        $this->template->assign('legalstatus', $legalstatus);
        $this->template->assign('sizes',       $sizes);
        return true;
    }

    /**
     * Affichage du détail d'une entreprise. 
     *
     * @param array $get
     * @param array $post
     * @param array $errors
     * @return boolean
     */
    public function detailAction($get, $post, $errors)
    {
        Afup::includeClass('Afup_Directory');
        Afup::includeClass('Afup_Directory_Config');
        //$params = array('criteria' => array('Valide' => '1'));

        $member = $this->getAfupDirectory()->getDirectoryMember($get['id']);
        $this->template->assign('member', $member);
        $this->template->assign('formeJuridique', Afup_Directory_Config::getLegalStatus($member->getFormeJuridique()));
        $this->template->assign('zone',           Afup_Directory_Config::getZone($member->getZone()));
        $this->template->assign('tailleSociete',  Afup_Directory_Config::getCompanySize($member->getTailleSociete()));
        return $member;
    }

    /**
     * Contact d'une entreprise.
     *
     * @throws Afup_Directory_Exception
     * @param array $get
     * @param array $post
     * @param array $errors
     * @return boolean
     */
    public function contactAction($get, $post, $errors)
    {
        if (isset($post['id']) && $post['id']) {
            $get['id'] = $post['id'];
        }
        $member = $this->getAfupDirectory()->getDirectoryMember($get['id']);
        $this->template->assign('member', $member);
        if (isset($post['contact']) && $post['contact']) {
            $contact = new Afup_Directory_Contact($post['contact']);
            $contact->setMember($member);
            $errors = $contact->validate();
            if ($errors->getNbErrors() > 0) {
                $this->template->assign('errors', $errors->getErrors());
            } else {
                $time = time();
                $serializedData = serialize($contact->toArray());
                $contactKey = md5($serializedData);
                $newLine = $time . '|||' . $contactKey . '|||' . $serializedData;
                $lines = file_exists(self::MESSAGES_FILE) ? file(self::MESSAGES_FILE) : array();
                $this->cleanContactLines($lines, $time);
                $lines[] = $newLine;
                $this->saveContacts($lines);

                // Envoi du message
                $this->template->assign('contactKey', $contactKey);
                $this->template->assign('message', $contact->getMessage());
                $this->template->assign('raisonSociale', $member->getRaisonSociale());

                $message = utf8_decode($this->template->fetch('mail_contact.txt'));

                if (function_exists('mail')) {
                    $headers = 'From: bureau@afup.org' . "\n" . 'Reply-To: bureau@afup.org';
                    mail($contact->getMail1(), "[ANNUAIRE AFUP] Confirmation d'envoi d'un message", $message, $headers);
                } else {
                    throw new Afup_Directory_Exception('Emails are not allowed');
                }

                $this->template->assign('mail_ok', true);
            }
        }
    }

    /**
     * Envoi d'un message
     *
     * @throws Afup_Directory_Exception
     * @param array $get
     * @param array $post
     * @param array $errors
     * @return boolean
     */
    public function sendAction($get, $post, $errors)
    {
        $key = (string) $get['key'];
        $errorMessage = "Le message n'est pas disponible, il se peut qu'il soit expiré ou qu'il ait déjà été envoyé.";
        if (!file_exists(self::MESSAGES_FILE)) {
            $this->template->assign('errors', array($errorMessage));
            return false;
        }
        $lines = file(self::MESSAGES_FILE);
        $this->cleanContactLines($lines);
        foreach ($lines as $lineKey => $line) {
            $tabLine = explode('|||', $line);

            // Ne pas mettre de break pour nettoyer les messages en double
            if ($tabLine[1] === $key) {
                $message = unserialize($tabLine[2]);
                unset($lines[$lineKey]);
            }
        }
        $this->saveContacts($lines);
        if (!isset($message['message']) && !$mesage['message']) {
            $this->template->assign('errors', array($errorMessage));
            return false;
        }

        if (function_exists('mail')) {
            $headers = 'From: ' . $message['from'] . "\n" . 'Reply-To: ' . $message['from'];
            mail($message['to'], "[ANNUAIRE AFUP] Contact", utf8_decode(stripslashes(str_replace('###', "\n", $message['message']))), $headers);
        } else {
            throw new Afup_Directory_Exception('Emails are not allowed');
        }
        return true;
    }

    /**
     * Formulaire d'édition. 
     *
     * @param array $get
     * @param array $post
     * @param array $errors
     * @return boolean
     * @todo aller chercher l'e-mail dans le fichier de configuration
     */
    public function editAction($get, $post, $errors)
    {
        $errors = array();

        // Input check
        if (isset($_POST['FormeJuridique'])) {
            if (get_magic_quotes_gpc()) {
                foreach ($_POST as $key => $value) {
                    if (!is_array($value)) {
                        $_POST[$key] = stripslashes($value);
                    }
                }
            }
            if (!Afup_Directory_Config::getLegalStatus($_POST['FormeJuridique'])) {
                $errors[] = "La forme juridique sélectionnée n'est pas correcte.";
            }
            if (!$_POST['RaisonSociale']) {
                $errors[] = "Veuillez spécifier la raison sociale (obligatoire).";
            }
            if (!$_POST['SIREN'] || !preg_match('/^[0-9 ]{9,17}$/', $_POST['SIREN'])) {
                $errors[] = "Veuillez spécifier le numéro SIREN/SIRET valide (obligatoire).";
            }
            if (!$_POST['Email'] || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,5}$/', $_POST['Email'])) {
                $errors[] = "Veuillez spécifier un e-mail valide (obligatoire).";
            }
            if (!$_POST['SiteWeb'] || !preg_match('#^http://.*$#', $_POST['SiteWeb'])) {
                $errors[] = "Veuillez spécifier un site web valide (obligatoire).";
            }
            if (!$_POST['Telephone'] || !preg_match('#[() +0-9]+$#', $_POST['Telephone'])) {
                $errors[] = "Veuillez spécifier un numéro de téléphone valide (obligatoire).";
            }
            if ($_POST['Fax'] && !preg_match('#[() +0-9]+$#', $_POST['Fax'])) {
                $errors[] = "Veuillez spécifier un numéro de fax valide.";
            }
            if (!$_POST['Adresse']) {
                $errors[] = "Veuillez spécifier une adresse (obligatoire).";
            }
            if (!$_POST['CodePostal'] || !preg_match('#[0-9]{5}$#', $_POST['CodePostal'])) {
                $errors[] = "Veuillez spécifier un code postal valide (obligatoire).";
            }
            if (!$_POST['Ville']) {
                $errors[] = "Veuillez spécifier une ville (obligatoire).";
            }
            if (!Afup_Directory_Config::getZone($_POST['Zone'])) {
                $errors[] = "Veuillez spécifier une zone correcte (obligatoire).";
            }
            if (!Afup_Directory_Config::getActivities($_POST['ActivitePrincipale'])) {
                $errors[] = "Veuillez spécifier une activité principale de la liste (obligatoire).";
            }
            if (isset($_POST['ActivitesSecondaires']) && count($_POST['ActivitesSecondaires']) && is_array($_POST['ActivitesSecondaires'])) {
                foreach ($_POST['ActivitesSecondaires'] as $activity) {
                    if (!Afup_Directory_Config::getActivities($activity)) {
                        $errors[] = "Au moins une de vos activités secondaires n'est pas correcte.";
                        break;
                    }
                }
            }
            if ($_POST['NumFormateur'] && !preg_match('#[0-9]+$#', $_POST['NumFormateur'])) {
                $errors[] = "Veuillez spécifier un numéro formateur valide.";
            }
            if (!$_POST['Password'] || strlen($_POST['Password']) < 5) {
                $errors[] = "Veuillez spécifier un mot de passe ayant au moins 5 caractères.";
            }
            if ($_POST['Password'] != $_POST['PasswordConfirmation']) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }
            $this->template->assign('values', $_POST);

            if (count($errors)) {
                $this->template->assign('formError', true);
                $this->template->assign('formErrors', $errors);
            }

            // Validation
            else {
                $this->template->assign('formError', false);
                unset($_POST['submit']);
                unset($_POST['PasswordConfirmation']);
                if ($siteNumber = Afup_Directory::getInstance($this->pdo)->addCompany($_POST) !== false) {
                    $this->template->assign('addAction', true);
                    $subject = utf8_decode("[ANNUAIRE AFUP] Nouveau site proposé dans l'annuaire");
                    $email = "bureau@afup.org";
                    $body  = "Un nouveau site vient d'être proposé dans l'annuaire professionnel.\n\n";
                    $body .= "Raison sociale : " . $_POST['RaisonSociale'] . "\n";
                    $body .= "Contact : " . $_POST['Email'];
                    $body = utf8_decode($body);
                    mail($email, $subject, $body);
                } else {
                    throw new Afup_Directory_Exception('AddCompany returns false');
                }
            }
        }

        // Data
        $choice = array('-- choisir --');
        $lists = array('legalStatus', 'zone', 'activities', 'companySize');
        foreach ($lists as $item) {
            $list = call_user_func(array('Afup_Directory_Config', 'get' . ucfirst($item)));
            ${$item . 'List'} = array_merge($choice, $list);
            $this->template->assign($item . 'List', ${$item . 'List'});
            if ($item == 'activities') {
                $this->template->assign($item . 'SecondList', $list);
            }
        }
    }

    // Méthodes utilitaires

    /**
     * Retrait des messages qui attendent depuis plus de 24 heures
     *
     * @param array $lines
     * @param integer $time
     */
    private function cleanContactLines(&$lines, $time = null)
    {
        if ($time === null) {
            $time = time();
        }
        foreach ($lines as $lineKey => $line) {
            $tabLine = explode('||', $line);
            if ($time - $tabLine[0] > 3600 * 24) {
                unset($lines[$lineKey]);
            }
        }
    }

    /**
     * Enregistrement du fichier contenant les messages contacts à envoyer.
     *
     * @param array $lines
     */
    private function saveContacts($lines)
    {
        $lines = implode("\n", $lines);
        file_put_contents(self::MESSAGES_FILE, $lines);
    }


    /**
     * Empêche la duplication par clonage.
     */
    private function __clone()
    {}

}