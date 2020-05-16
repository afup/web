<?php

// Voir la classe Afup\Site\Association\Personnes_Physiques
namespace Afup\Site\Association;

use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Association\Model\Repository\UserRepository;

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
                          $filtre = false,
                          $isActive = null
    )
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_morales ';
        $requete .= ' WHERE 1 = 1 ';

        if ($filtre) {
            $requete .= 'AND (raison_sociale LIKE \'%' . $filtre . '%\' ';
            $requete .= 'OR ville LIKE \'%' . $filtre . '%\' )';
        }

        if (null !== $isActive) {
            $requete .= ' AND afup_personnes_morales.etat = ' . $this->_bdd->echapper($isActive ? '1' : '0') . ' ';
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

    public function obtenirMembresMaximum($id, $default = 3)
    {
        $requete = "SELECT max_members FROM afup_personnes_morales";
        $requete .= " where afup_personnes_morales.id = " . (int)$id;
        $row = $this->_bdd->obtenirTous($requete);

        if (!isset($row[0])) {
            return $default;
        }

        if (!is_numeric($row[0]['max_members'])) {
            return $default;
        }

        return $row[0]['max_members'];
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
    function ajouter($civilite, $nom, $prenom, $email, $raison_sociale, $siret, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $etat, $max_members)
    {
        $requete = 'INSERT INTO
        afup_personnes_morales (civilite, nom, prenom, email, raison_sociale, siret, adresse, code_postal, ville, id_pays, telephone_fixe, telephone_portable, etat, max_members)
        VALUES (' . $civilite . ', ' .
        $this->_bdd->echapper($nom) . ', ' .
        $this->_bdd->echapper($prenom) . ', ' .
        $this->_bdd->echapper($email) . ', ' .
        $this->_bdd->echapper($raison_sociale) . ', ' .
        $this->_bdd->echapper($siret) . ', ' .
        $this->_bdd->echapper($adresse) . ', ' .
        $this->_bdd->echapper($code_postal) . ', ' .
        $this->_bdd->echapper($ville) . ', ' .
        $this->_bdd->echapper($id_pays) . ', ' .
        $this->_bdd->echapper($telephone_fixe) . ', ' .
        $this->_bdd->echapper($telephone_portable) . ', ' .
        $this->_bdd->echapper($etat) . ', ' .
        $this->_bdd->echapper($max_members) . ')';
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
    function modifier($id, $civilite, $nom, $prenom, $email, $raison_sociale, $siret, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $etat, $max_members = null)
    {
        $requete  = 'UPDATE 
          afup_personnes_morales 
        SET
          civilite=' . $civilite . ',
          nom=' . $this->_bdd->echapper($nom) . ',
          prenom=' . $this->_bdd->echapper($prenom) . ',
          email=' . $this->_bdd->echapper($email) . ',
          raison_sociale=' . $this->_bdd->echapper($raison_sociale) . ',
          siret=' . $this->_bdd->echapper($siret) . ',
          adresse=' . $this->_bdd->echapper($adresse) . ',
          code_postal=' . $this->_bdd->echapper($code_postal) . ',
          ville=' . $this->_bdd->echapper($ville) . ',
          id_pays=' . $this->_bdd->echapper($id_pays) . ',
          telephone_fixe=' . $this->_bdd->echapper($telephone_fixe) . ',
          telephone_portable=' . $this->_bdd->echapper($telephone_portable) . ',
          etat=' . $this->_bdd->echapper($etat) ;
        if ($max_members !== null) {
            $requete .= ', max_members = ' . $this->_bdd->echapper($max_members);
        }
        $requete .= 'WHERE  id=' . (int)$id;
        return $this->_bdd->executer($requete);
    }

    /**
     * Supprime une personne morale
     *
     * @param  int $id Identifiant de la personne morale à supprimer
     * @access public
     * @return bool     Succès de la suppression
     */
    function supprimer($id, UserRepository $userRepository)
    {
        $cotisation = new Cotisations($this->_bdd);
        $cotisation_personne_morale = $cotisation->obtenirListe(AFUP_PERSONNES_MORALES, $id, 'id');
        $users = $userRepository->search('lastname', 'asc', null, $id);
        if (count($cotisation_personne_morale) === 0 && count($users) === 0) {
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

    public function getMembershipFee($id)
    {
        $membersCount = $this->obtenirMembresMaximum($id);

        return ceil($membersCount / AFUP_PERSONNE_MORALE_SEUIL) * AFUP_COTISATION_PERSONNE_MORALE;
    }
}

?>
