<?php

declare(strict_types=1);

namespace Afup\Site\Forum;

class Coupon
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     object
     */
    private $_bdd;

    /**
     * Constructeur.
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @return void
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant un forum
     *
     * @param  int $id Identifiant du forum
     * @param  string $champs Champs à renvoyer
     * @return array
     */
    public function obtenir($id, string $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_forum_coupon ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public function obtenirCouponsForum(string $id_forum)
    {
        $requete = 'SELECT';
        $requete .= '  id, texte ';
        $requete .= 'FROM';
        $requete .= '  afup_forum_coupon ';
        $requete .= 'WHERE id_forum=' . $id_forum;
        return $this->_bdd->obtenirAssociatif($requete);
    }

    public function ajouter($id_forum, $texte)
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_forum_coupon (id, id_forum, texte) ';
        $requete .= 'VALUES (null,';
        $requete .= (int) $id_forum . ',';
        $requete .= $this->_bdd->echapper(strtoupper($texte)) . ')';
        return $this->_bdd->executer($requete);
    }

    public function supprimer(string $id)
    {
        $requete = 'DELETE FROM afup_forum_coupon WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }

    public function supprimerParForum(string $id_forum)
    {
        $requete = 'DELETE FROM afup_forum_coupon WHERE id_forum =' . $id_forum;
        return $this->_bdd->executer($requete);
    }
}
