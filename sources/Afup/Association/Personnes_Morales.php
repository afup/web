<?php

// Voir la classe Afup\Site\Association\Personnes_Physiques
namespace Afup\Site\Association;

use Afup\Site\Utils\Base_De_Donnees;

define('AFUP_PERSONNES_MORALES', 1);
define('AFUP_COTISATION_PERSONNE_MORALE', 100);

/**
 * Classe de gestion des personnes morales
 */
class Personnes_Morales
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     Base_De_Donnees
     * @access  private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param  Base_De_Donnees $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     */
    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit la liste des personnes morales
     *
     * @param  string $champs Champs à renvoyer
     * @param  string $ordre Tri des enregistrements
     * @param  bool $associatif Renvoyer un tableau associatif ?
     * @access public
     * @return array
     */
    function obtenirListe($champs = '*',
                          $ordre = 'raison_sociale',
                          $associatif = false,
                          $filtre = false)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_morales ';
        if ($filtre) {
            $requete .= 'WHERE raison_sociale LIKE \'%' . $filtre . '%\' ';
            $requete .= 'OR ville LIKE \'%' . $filtre . '%\' ';
        }
        $requete .= 'ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            $list = $this->_bdd->obtenirTous($requete);
            foreach ($list as &$morales) {
                $morales['actifs'] = $this->obtenirActifs($morales['id']);
            }
            return $list;
        }
    }

    /**
     * Obtenir le nombre d'actifs d'une personne morale
     *
     * @param  integer $id ID de la personne morale
     * @access public
     * @return int le nombre d'actifs
     */
    function obtenirActifs($id)
    {
        $requete = "SELECT count(*) FROM afup_personnes_morales m, afup_personnes_physiques p ";
        $requete .= "where m.id = $id and m.id = p.id_personne_morale and p.etat = 1";
        $actifs = $this->_bdd->obtenirTous($requete);

        return (int)current($actifs[0]);
    }

    /**
     * Renvoit les informations concernant une personne morale
     *
     * @param  int $id Identifiant de la personne
     * @param  string $champs Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenir($id, $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_morales ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Ajoute une personne morale
     *
     * @param  string $raison_sociale Raison sociale de la personne morale
     * @param  string $adresse Adresse de la personne morale
     * @param  string $code_postal Code postal de la personne morale
     * @param  string $ville Ville de la personne morale
     * @param  int $id_pays Identifiant du pays de la personne morale
     * @param  int $etat Etat de la personne morale
     * @access public
     * @return bool     Succès de l'ajout
     */
    function ajouter($civilite, $nom, $prenom, $email, $raison_sociale, $siret, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $etat)
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_personnes_morales (civilite, nom, prenom, email, raison_sociale, siret, adresse, code_postal, ville, id_pays, telephone_fixe, telephone_portable, etat) ';
        $requete .= 'VALUES (';
        $requete .= $civilite . ',';
        $requete .= $this->_bdd->echapper($nom) . ',';
        $requete .= $this->_bdd->echapper($prenom) . ',';
        $requete .= $this->_bdd->echapper($email) . ',';
        $requete .= $this->_bdd->echapper($raison_sociale) . ',';
        $requete .= $this->_bdd->echapper($siret) . ',';
        $requete .= $this->_bdd->echapper($adresse) . ',';
        $requete .= $this->_bdd->echapper($code_postal) . ',';
        $requete .= $this->_bdd->echapper($ville) . ',';
        $requete .= $this->_bdd->echapper($id_pays) . ',';
        $requete .= $this->_bdd->echapper($telephone_fixe) . ',';
        $requete .= $this->_bdd->echapper($telephone_portable) . ',';
        $requete .= $etat . ')';
        return $this->_bdd->executer($requete);

    }


    /**
     * Modifie une personne morale
     *
     * @param  int $id Identifiant de la personne morale à modifier
     * @param  string $raison_sociale Raison sociale de la personne morale
     * @param  string $adresse Adresse de la personne morale
     * @param  string $code_postal Code postal de la personne morale
     * @param  string $ville Ville de la personne morale
     * @param  int $id_pays Identifiant du pays de la personne morale
     * @param  int $etat Etat de la personne morale
     * @access public
     * @return bool     Succès de l'ajout
     */
    function modifier($id, $civilite, $nom, $prenom, $email, $raison_sociale, $siret, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $etat)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_personnes_morales ';
        $requete .= 'SET';
        $requete .= '  civilite=' . $civilite . ',';
        $requete .= '  nom=' . $this->_bdd->echapper($nom) . ',';
        $requete .= '  prenom=' . $this->_bdd->echapper($prenom) . ',';
        $requete .= '  email=' . $this->_bdd->echapper($email) . ',';
        $requete .= '  raison_sociale=' . $this->_bdd->echapper($raison_sociale) . ',';
        $requete .= '  siret=' . $this->_bdd->echapper($siret) . ',';
        $requete .= '  adresse=' . $this->_bdd->echapper($adresse) . ',';
        $requete .= '  code_postal=' . $this->_bdd->echapper($code_postal) . ',';
        $requete .= '  ville=' . $this->_bdd->echapper($ville) . ',';
        $requete .= '  id_pays=' . $this->_bdd->echapper($id_pays) . ',';
        $requete .= '  telephone_fixe=' . $this->_bdd->echapper($telephone_fixe) . ',';
        $requete .= '  telephone_portable=' . $this->_bdd->echapper($telephone_portable) . ',';
        $requete .= '  etat=' . $this->_bdd->echapper($etat) . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;
        return $this->_bdd->executer($requete);
    }

    /**
     * Supprime une personne morale
     *
     * @param  int $id Identifiant de la personne morale à supprimer
     * @access public
     * @return bool     Succès de la suppression
     */
    function supprimer($id)
    {

        $cotisation = new Cotisations($this->_bdd);
        $cotisation_personne_morale = $cotisation->obtenirListe(AFUP_PERSONNES_MORALES, $id, 'id');

        $personne_physique = new Personnes_Physiques($this->_bdd);
        $personne_physique_de_personne_morale = $personne_physique->obtenirListe('id', 'nom', '', $id);

        if (sizeof($cotisation_personne_morale) == 0 and sizeof($personne_physique_de_personne_morale) == 0) {
            $requete = 'DELETE FROM afup_personnes_morales WHERE id=' . $id;
            return $this->_bdd->executer($requete);
        }

        return false;
    }

    /**
     * Retourne le nombre de personnes morales.
     *
     * @param   int $etat Etat des personnes à retourner
     * @access  public
     * @return  int
     */
    function obtenirNombrePersonnesMorales($etat = NULL)
    {
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_morales ';

        if (!is_null($etat)) {
            $requete .= 'WHERE';
            $requete .= '  etat=' . $etat;
        }

        return $this->_bdd->obtenirUn($requete);
    }
}

?>