<?php

namespace Afup\Site\Association;

use Afup\Site\Utils\Mail;
use Afup\Site\Utils\Mailing;

/**
 * Classe de gestion des personnes physiques
 */
class Personnes_Physiques
{
    /**
     * Instance de la couche d'abstraction à la base de données
     *
     * @var \Afup\Site\Utils\Base_De_Donnees
     * @access private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit la liste des personnes physiques
     *
     * @param string $champs Champs à renvoyer
     * @param string $ordre Tri des enregistrements
     * @access public
     * @return array
     */
    function obtenirListe($champs = '*',
                          $ordre = 'nom, prenom',
                          $filtre = false,
                          $id_personne_morale = false,
                          $associatif = false,
                          $id_personne_physique = false,
                          $is_active = NULL,
                          $a_jour_de_cotisation = null
    )
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE 1 = 1 ';

        // On filtre sur tous les mots possibles. Donc plus on a de mots dans la recherche plus on aura de résultats.
        // Mais ça peut aussi permettre de trouver des personnes en entrant par exemple "Prénom email" dans le champ de recherche :
        //   Même si l'email ne colle pas on pourra trouver la personne.
        // C'est un peu barbare mais généralement on ne met qu'un seul terme dans la recherche… du coup c'est pas bien grave.
        if ($filtre) {
            $filtres = explode(' ', $filtre);
            $filtres = array_map('trim', $filtres);
            $requests = [];
            foreach ($filtres as $filtre) {
                if (!$filtre) {
                    continue;
                }
                $filtre  = $this->_bdd->echapper('%' . $filtre . '%');
                $requests[] = <<<SQL
(
    login LIKE $filtre
    OR nom LIKE $filtre
    OR prenom LIKE $filtre
    OR code_postal LIKE $filtre
    OR ville LIKE $filtre
    OR email LIKE $filtre
)
SQL;
            }
            $requete .= " AND (" . implode(" OR ", $requests) . ") ";
        }
        if ($id_personne_morale) {
            $requete .= 'AND id_personne_morale = ' . $id_personne_morale . ' ';
        }
        if ($id_personne_physique) {
            if (!is_array($id_personne_physique)) {
                $id_personne_physique = array($id_personne_physique);
            }
            $requete .= 'AND id IN (' . join(",", $id_personne_physique) . ') ';
        }
        if ($is_active !== NULL) {
            $requete .= 'AND etat = ' . $is_active . ' ';
        }

        if ($a_jour_de_cotisation) {
            $requete .= strtr(
               " AND
               (
                  id IN (
                    SELECT id_personne
                    FROM afup_cotisations
                    WHERE NOW() BETWEEN FROM_UNIXTIME(date_debut) AND FROM_UNIXTIME(date_fin)
                    AND type_personne = :id_personne_physiques:
                  )
                  OR id_personne_morale IN (
                    SELECT id_personne
                    FROM afup_cotisations
                    WHERE NOW() BETWEEN FROM_UNIXTIME(date_debut) AND FROM_UNIXTIME(date_fin)
                    AND type_personne = :id_personne_morale:
                  )
               )
               ",
               [
                   ':id_personne_physiques:' => AFUP_PERSONNES_PHYSIQUES,
                   ':id_personne_morale:' => AFUP_PERSONNES_MORALES,
               ]
            );
        }

        $requete .= 'ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    /**
     * Renvoit les informations concernant une personne physique
     *
     * @param int $id Identifiant de la personne
     * @param string $champs Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenir($id, $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE id=' . $id;
        $resultats = $this->_bdd->obtenirEnregistrement($requete);

        /*On redivise les droits sur les modules*/
        if (isset($resultats['niveau_modules'][0])) {
            $resultats['niveau_apero'] = $resultats['niveau_modules'][0];
        }
        if (isset($resultats['niveau_modules'][1])) {
            $resultats['niveau_annuaire'] = $resultats['niveau_modules'][1];
        }
        if (isset($resultats['niveau_modules'][2])) {
            $resultats['niveau_site'] = $resultats['niveau_modules'][2];
        }
        if (isset($resultats['niveau_modules'][3])) {
            $resultats['niveau_forum'] = $resultats['niveau_modules'][3];
        }
        unset($resultats["niveau_modules"]);

        return $resultats;
    }

    /**
     * Ajoute une personne physique
     *
     * @param  int $id_personne_morale Identifiant de la personne morale à laquelle est liée la personne physique
     * @param  string $login Login de la personne physique
     * @param  string $mot_de_passe Mot de passe de la personne physique
     * @param  int $niveau Niveau de droits de la personne physique
     * @param  string $niveau_modules Niveau de droits sur les différents modules de la personne physique
     * @param  string $civilite Civilité de la personne physique
     * @param  string $nom Nom de la personne physique
     * @param  string $prenom Prénom de la personne physique
     * @param  string $email Email de la personne physique
     * @param  string $adresse Adresse de la personne physique
     * @param  string $code_postal Code postal de la personne physique
     * @param  string $ville Ville de la personne physique
     * @param  int $id_pays Identifiant du pays de la personne physique
     * @param  string $telephone_fixe Téléphone fixe de la personne physique
     * @param  string $telephone_portable Téléphone portable de la personne physique
     * @param  int $etat Etat de la personne physique
     * @param $compte_svn
     * @param bool $throwsException
     * @return bool Succès de l'ajout
     * @throws \Exception
     */
    public function ajouter($id_personne_morale, $login, $mot_de_passe, $niveau, $niveau_modules, $civilite, $nom, $prenom,
                            $email, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $etat, $compte_svn, $nearest_office = null, $throwsException = false)
    {
        if (empty($id_personne_morale)) {
            $id_personne_morale = null;
        }

        $erreur = $this->loginExists(0, $login, $throwsException);
        $erreur = $erreur || $this->_emailExists(0, $email, $throwsException);
        $erreur = $erreur || !$this->_companyExists($id_personne_morale, $throwsException);
        $erreur = $erreur || !$this->_countryExists($id_pays, $throwsException);

        if (!$erreur) {
            $requete = 'INSERT INTO ';
            $requete .= '  afup_personnes_physiques (id_personne_morale, login, mot_de_passe, niveau, niveau_modules, civilite, nom, prenom, email, ';
            $requete .= '  adresse, code_postal, ville, id_pays, telephone_fixe, telephone_portable, etat, nearest_office, compte_svn) ';
            $requete .= 'VALUES (';
            $requete .= (int)$id_personne_morale . ',';
            $requete .= $this->_bdd->echapper($login) . ',';
            $requete .= $this->_bdd->echapper($mot_de_passe) . ',';
            $requete .= (int)$niveau . ',';
            $requete .= $this->_bdd->echapper($niveau_modules) . ',';
            $requete .= $this->_bdd->echapper($civilite) . ',';
            $requete .= $this->_bdd->echapper($nom) . ',';
            $requete .= $this->_bdd->echapper($prenom) . ',';
            $requete .= $this->_bdd->echapper($email) . ',';
            $requete .= $this->_bdd->echapper($adresse) . ',';
            $requete .= $this->_bdd->echapper($code_postal) . ',';
            $requete .= $this->_bdd->echapper($ville) . ',';
            $requete .= $this->_bdd->echapper($id_pays) . ',';
            $requete .= $this->_bdd->echapper($telephone_fixe) . ',';
            $requete .= $this->_bdd->echapper($telephone_portable) . ',';
            $requete .= (int)$etat . ',';
            $requete .= $this->_bdd->echapper($nearest_office) . ',';
            $requete .= $this->_bdd->echapper($compte_svn) . ')';

            $inserted = $this->_bdd->executer($requete);

            if (!$inserted && $throwsException) {
                throw new \Exception("Impossible d'enregistrer l'utilisateur à cause d'une erreur SQL. Veuillez contacter le bureau !");
            }

            return $inserted;
        }

        return false;
    }

    /**
     * Modifie une personne physique
     *
     * @param  int $id Identifiant de la personne physique
     * @param  int $id_personne_morale Identifiant de la personne morale à laquelle est liée la personne physique
     * @param  string $login Login de la personne physique
     * @param  string $mot_de_passe Mot de passe de la personne physique
     * @param  int $niveau Niveau de droits de la personne physique
     * @param  string $niveau_modules Niveau de droits sur les différents modules de la personne physique
     * @param  string $civilite Civilité de la personne physique
     * @param  string $nom Nom de la personne physique
     * @param  string $prenom Prénom de la personne physique
     * @param  string $email Email de la personne physique
     * @param  string $adresse Adresse de la personne physique
     * @param  string $code_postal Code postal de la personne physique
     * @param  string $ville Ville de la personne physique
     * @param  int $id_pays Identifiant du pays de la personne physique
     * @param  string $telephone_fixe Téléphone fixe de la personne physique
     * @param  string $telephone_portable Téléphone portable de la personne physique
     * @param  int $etat Etat de la personne physique
     * @access public
     * @return bool Succès de la modification
     */
    function modifier($id, $id_personne_morale, $login, $mot_de_passe, $niveau, $niveau_modules, $civilite, $nom, $prenom,
                      $email, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $etat, $compte_svn, $roles)
    {
        $erreur = $this->loginExists($id, $login);
        $erreur = $erreur || !$this->_companyExists($id_personne_morale);
        $erreur = $erreur || !$this->_countryExists($id_pays);

        if (!$erreur) {
            $requete = 'UPDATE ';
            $requete .= '  afup_personnes_physiques ';
            $requete .= 'SET';

            if (!empty($mot_de_passe)) {
                $requete .= '  mot_de_passe=' . $this->_bdd->echapper(md5($mot_de_passe)) . ',';
            }

            $requete .= '  id_personne_morale=' . (int)$id_personne_morale . ',';
            $requete .= '  login=' . $this->_bdd->echapper($login) . ',';
            $requete .= '  niveau=' . $this->_bdd->echapper($niveau) . ',';
            $requete .= '  niveau_modules=' . $this->_bdd->echapper($niveau_modules) . ',';
            $requete .= '  civilite=' . $this->_bdd->echapper($civilite) . ',';
            $requete .= '  nom=' . $this->_bdd->echapper($nom) . ',';
            $requete .= '  prenom=' . $this->_bdd->echapper($prenom) . ',';
            $requete .= '  email=' . $this->_bdd->echapper($email) . ',';
            $requete .= '  adresse=' . $this->_bdd->echapper($adresse) . ',';
            $requete .= '  code_postal=' . $this->_bdd->echapper($code_postal) . ',';
            $requete .= '  ville=' . $this->_bdd->echapper($ville) . ',';
            $requete .= '  id_pays=' . $this->_bdd->echapper($id_pays) . ',';
            $requete .= '  telephone_fixe=' . $this->_bdd->echapper($telephone_fixe) . ',';
            $requete .= '  telephone_portable=' . $this->_bdd->echapper($telephone_portable) . ',';
            $requete .= '  etat=' . $this->_bdd->echapper($etat) . ',';
            if ($roles !== null) {
                if (@json_decode($roles) === null) {
                    return false;
                }
                $requete .= '  roles=' . $this->_bdd->echapper($roles) . ',';
            }
            $requete .= '  compte_svn=' . $this->_bdd->echapper($compte_svn) . ' ';
            $requete .= 'WHERE';
            $requete .= '  id=' . $id;

            return $this->_bdd->executer($requete);
        }

        return false;
    }

    /**
     * Modifie seulement les coordonnées d'une personne physique
     *
     * @param int $id Identifiant de la personne physique
     * @param string $login Login de la personne physique
     * @param string $mot_de_passe Mot de passe de la personne physique
     * @param string $email Email de la personne physique
     * @param string $adresse Adresse de la personne physique
     * @param string $code_postal Code postal de la personne physique
     * @param string $ville Ville de la personne physique
     * @param int $id_pays Identifiant du pays de la personne physique
     * @param string $telephone_fixe Téléphone fixe de la personne physique
     * @param string $telephone_portable Téléphone portable de la personne physique
     * @access public
     * @return bool Succès de la modification
     */
    function modifierCoordonnees($id, $login, $mot_de_passe,
                                 $email, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $nearest_office = '')
    {
        $erreur = $this->loginExists($id, $login);
        $erreur = $erreur || !$this->_countryExists($id_pays);

        if (!$erreur) {
            $requete = 'UPDATE ';
            $requete .= '  afup_personnes_physiques ';
            $requete .= 'SET';

            if (!empty($mot_de_passe)) {
                $requete .= '  mot_de_passe=' . $this->_bdd->echapper(md5($mot_de_passe)) . ',';
            }
            $requete .= '  login=' . $this->_bdd->echapper($login) . ',';
            $requete .= '  email=' . $this->_bdd->echapper($email) . ',';
            $requete .= '  adresse=' . $this->_bdd->echapper($adresse) . ',';
            $requete .= '  code_postal=' . $this->_bdd->echapper($code_postal) . ',';
            $requete .= '  ville=' . $this->_bdd->echapper($ville) . ',';
            $requete .= '  id_pays=' . $this->_bdd->echapper($id_pays) . ',';
            $requete .= '  nearest_office=' . $this->_bdd->echapper($nearest_office) . ',';
            $requete .= '  telephone_fixe=' . $this->_bdd->echapper($telephone_fixe) . ',';
            $requete .= '  telephone_portable=' . $this->_bdd->echapper($telephone_portable);
            $requete .= 'WHERE';
            $requete .= '  id=' . $id;
            return $this->_bdd->executer($requete);
        }

        return false;
    }

    /**
     * Envoi un nouveau mot de passe lorsque l'utilisateur le demande
     *
     * @param string $email Email de la personne physique
     * @access public
     * @return bool Succès de l'envoi
     */
    function envoyerMotDePasse($email, $id = null)
    {
        $succes = false;

        $selection = 'SELECT ';
        $selection .= ' id, login, email ';
        $selection .= 'FROM ';
        $selection .= '  afup_personnes_physiques ';
        $selection .= 'WHERE ';
        if ($id === null) {
            $selection .= '  email=' . $this->_bdd->echapper($email);
        } else {
            $selection .= '  id=' . $this->_bdd->echapper($id) . ' ';
        }
        $data = $this->_bdd->obtenirEnregistrement($selection);
        $id = $data['id'];
        $identifiant = $data['login'];
        $email = $data['email'];
        if (is_numeric($id) and $id > 0) {
            $mot_de_passe = substr(md5(uniqid(rand(), true)), 0, 10);

            $requete = 'UPDATE ';
            $requete .= '  afup_personnes_physiques ';
            $requete .= 'SET';
            $requete .= '  mot_de_passe=' . $this->_bdd->echapper(md5($mot_de_passe));
            $requete .= 'WHERE';
            $requete .= '  id=' . $this->_bdd->echapper($id);

            if ($this->_bdd->executer($requete)) {
                $corps = "Votre nouveau mot de passe est indiqué ci-dessous. \n";
                $corps .= "Il vous permettra de vous connecter dès maintenant au site de l'AFUP.\n\n";
                $corps .= "Votre identifiant : $identifiant \n";
                $corps .= "Votre mot de passe : $mot_de_passe \n\n";
                $corps .= "https://afup.org/admin/login";


                $check = Mailing::envoyerMail(
                    $GLOBALS['conf']->obtenir('mails|email_expediteur'),
                    $email,
                    "AFUP : Mot de passe perdu ?",
                    $corps);

                return ($check);

            }
        }

        return $succes;
    }

    /**
     * Envoi un message de bienvenue lorsque l'utilisateur le demande
     *
     * @param int $id Id du compte
     * @access public
     * @return bool Succès de l'envoi
     */
    function generatePassword($id = null)
    {
        $selection = 'SELECT ';
        $selection .= ' id, login, email, prenom, nom ';
        $selection .= 'FROM ';
        $selection .= '  afup_personnes_physiques ';
        $selection .= 'WHERE ';
        $selection .= '  id=' . $this->_bdd->echapper($id) . ' ';
        $data = $this->_bdd->obtenirEnregistrement($selection);

        $id = $data['id'];
        $identifiant = $data['login'];
        $email = $data['email'];

        if (is_numeric($id) and $id > 0) {
            $mot_de_passe = substr(md5(uniqid(rand(), true)), 0, 10);

            $query = 'UPDATE ';
            $query .= '  afup_personnes_physiques ';
            $query .= 'SET';
            $query .= '  mot_de_passe=' . $this->_bdd->echapper(md5($mot_de_passe));
            $query .= 'WHERE';
            $query .= '  id=' . $this->_bdd->echapper($id);

            if ($this->_bdd->executer($query)) {
                return $mot_de_passe;
            }
        }

        return false;
    }

    public function sendWelcomeMailWithData($firstName, $lastName, $login, $email)
    {
        $mail = new Mail(null, null);
        return $mail->send(
            'mail_templates:confirmation_creation_compte.html.twig',
            ['email' => $email, 'name' => sprintf('%s %s', $firstName, $lastName)],
            ['login' => $login],
            [
                'subject' => 'Votre compte afup.org',
                'from' => [
                    'email' => 'bureau@afup.org',
                    'name' => 'AFUP'
                ]
            ]
        );
    }

    /**
     * Supprime une personne physique
     *
     * @param int $id Identifiant de la personne physique
     * @access public
     * @return bool Succès de la suppresion
     */
    function supprimer($id)
    {

        $cotisation = new Cotisations($this->_bdd);
        $cotisation_personne_physisque = $cotisation->obtenirListe(AFUP_PERSONNES_PHYSIQUES, $id, 'id');

        if (sizeof($cotisation_personne_physisque) == 0) {
            $requete = 'DELETE FROM afup_personnes_physiques WHERE id=' . $id;
            return $this->_bdd->executer($requete);
        }

        return false;
    }

    /**
     * Renvoie la dernière cotisation
     *
     * @param int $id Identifiant de la personne physique
     * @access public
     * @return array
     */
    function obtenirDerniereCotisation($id_personne_physique)
    {


        $requete = 'SELECT';
        $requete .= '  id_personne_morale ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  id = ' . $id_personne_physique;
        $id_personne_morale = $this->_bdd->obtenirUn($requete);

        $cotisation = new Cotisations($this->_bdd);
        if ($id_personne_morale > 0) {
            $id = $id_personne_morale;
            $type_personne = AFUP_PERSONNES_MORALES;
        } else {
            $id = $id_personne_physique;
            $type_personne = AFUP_PERSONNES_PHYSIQUES;
        }

        return $cotisation->obtenirDerniere($type_personne, $id);
    }

    /**
     * Retourne le nombre de membres (personnes physiques qu'elles soient ou non
     * liées à une personne morale).
     *
     * @param int $etat Etat des membres à retourner
     * @access public
     * @return int
     */
    function obtenirNombreMembres($etat = null)
    {
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';

        if (!is_null($etat)) {
            $requete .= 'WHERE';
            $requete .= '  etat=' . $etat;
        }

        return $this->_bdd->obtenirUn($requete);
    }

    /**
     * Retourne le nombre de personnes physiques non liées à une personne
     * morale.
     *
     * @param int $etat Etat des personnes à retourner
     * @access public
     * @return int
     */
    function obtenirNombrePersonnesPhysiques($etat = null)
    {
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  id_personne_morale = 0';

        if (!is_null($etat)) {
            $requete .= '  AND etat=' . $etat;
        }

        return $this->_bdd->obtenirUn($requete);
    }

    /**
     * Returns if the email is already in use or not.
     *
     * @param int $id Identifier to ignore
     * @param string $email Person's email
     * @param bool $throwsExceptionIfExists Throws exception if the email is already in use
     * @return bool TRUE if the email exists, FALSE otherwise
     * @throws \Exception
     */
    protected function _emailExists($id, $email, $throwsExceptionIfExists = false)
    {
        $requete = 'SELECT 1 ';
        $requete .= 'FROM afup_personnes_physiques ';
        $requete .= 'WHERE email=' . $this->_bdd->echapper($email) . ' AND id <> ' . intval($id);

        $exists = ($this->_bdd->obtenirUn($requete) !== false);

        if ($exists && $throwsExceptionIfExists) {
            throw new \Exception("Il existe un compte avec cette adresse email.");
        }

        return $exists;
    }

    /**
     * Returns if the login is already in use or not.
     *
     * @param int $id Identifier to ignore
     * @param string $login Person's login
     * @param bool $throwsExceptionIfExists Throws exception if the login is already in use
     * @return bool Login in use (TRUE) or not (FALSE)
     * @throws \Exception
     */
    public function loginExists($id, $login, $throwsExceptionIfExists = false)
    {
        $requete = 'SELECT 1 ';
        $requete .= 'FROM afup_personnes_physiques ';
        $requete .= 'WHERE login=' . $this->_bdd->echapper($login) . ' AND id <> ' . intval($id);

        $exists = ($this->_bdd->obtenirUn($requete) !== false);

        if ($exists && $throwsExceptionIfExists) {
            throw new \Exception("Il existe déjà un compte pour ce login.");
        }

        return $exists;
    }

    /**
     * Returns if the company exists.
     *
     * @param bool|int $id_personne_morale Company's identifier
     * @param bool $throwsExceptionIfDoesNotExist Throws exception if the company doesn't exist
     * @return bool TRUE if the company exists, FALSE otherwise
     * @throws \Exception
     */
    protected function _companyExists($id_personne_morale = false, $throwsExceptionIfDoesNotExist = false)
    {
        // Useless to check if no identifier provided
        if (empty($id_personne_morale)) {
            return true;
        }

        $requete = 'SELECT 1 ';
        $requete .= 'FROM afup_personnes_morales ';
        $requete .= 'WHERE id = ' . intval($id_personne_morale);

        $exists = ($this->_bdd->obtenirUn($requete) !== false);

        if (!$exists && $throwsExceptionIfDoesNotExist) {
            throw new \Exception("La personne morale n'existe pas.");
        }

        return $exists;
    }

    /**
     * Returns if the country exists.
     *
     * @param int $id_pays Country's identifier
     * @param bool $throwsExceptionIfDoesNotExist Throws exception if the country doesn't exist
     * @return bool TRUE if the country exists, FALSE otherwise
     * @throws \Exception
     */
    protected function _countryExists($id_pays, $throwsExceptionIfDoesNotExist = false)
    {
        // If no country provided, it exists… stupid rule but that's ok \o/
        if (empty($id_pays)) {
            return true;
        }

        $requete = 'SELECT 1 ';
        $requete .= 'FROM afup_pays ';
        $requete .= 'WHERE id = ' . intval($id_pays);

        $exists = ($this->_bdd->obtenirUn($requete) !== false);

        if (!$exists && $throwsExceptionIfDoesNotExist) {
            throw new \Exception("Le pays n'existe pas.");
        }

        return $exists;
    }

    function obtenirIdDepuisCompteSVN($compte_svn)
    {
        $requete = ' SELECT id ';
        $requete .= ' FROM afup_personnes_physiques ';
        $requete .= ' WHERE compte_svn = ' . $this->_bdd->echapper($compte_svn);

        return $this->_bdd->obtenirUn($requete);
    }

    public function getListeAvecDroitsAdministration()
    {
        $requete = ' SELECT * ';
        $requete .= ' FROM afup_personnes_physiques ';
        $requete .= ' WHERE niveau_modules <> 0 ';
        $requete .= ' OR niveau = 2 ';
        $requete .= ' ORDER BY nom, prenom ';

        $resultats = $this->_bdd->obtenirTous($requete);

        foreach ($resultats as &$r) {
            /*On redivise les droits sur les modules*/
            //$r['niveau_modules'][0] Anciennement apéro a été supprimé
            if (isset($r['niveau_modules'][1])) {
                $r['niveau_annuaire'] = $r['niveau_modules'][1];
            }
            if (isset($r['niveau_modules'][2])) {
                $r['niveau_site'] = $r['niveau_modules'][2];
            }
            if (isset($r['niveau_modules'][3])) {
                $r['niveau_forum'] = $r['niveau_modules'][3];
            }
            if (isset($r['niveau_modules'][4])) {
                $r['niveau_antenne'] = $r['niveau_modules'][4];
            }
            unset($r["niveau_modules"]);
        }

        return $resultats;
    }

    public function getUserByEmail($email)
    {
        $requete = ' SELECT * ';
        $requete .= ' FROM afup_personnes_physiques ';
        $requete .= ' WHERE email = ' . $this->_bdd->echapper($email);
        return $this->_bdd->obtenirEnregistrement($requete);
    }
}
