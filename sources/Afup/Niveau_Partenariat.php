<?php

declare(strict_types=1);

namespace Afup\Site;

class Niveau_Partenariat
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
     * Renvoit la liste des types de partenariat
     *
     * @return array
     */
    public function obtenirListe()
    {
        $requete = 'SELECT';
        $requete .= '  id, titre ';
        $requete .= 'FROM';
        $requete .= '  afup_niveau_partenariat ';
        $requete .= 'ORDER BY id';
        return $this->_bdd->obtenirAssociatif($requete);
    }
}
