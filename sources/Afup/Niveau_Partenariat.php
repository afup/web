<?php

declare(strict_types=1);

namespace Afup\Site;

use Afup\Site\Utils\Base_De_Donnees;

class Niveau_Partenariat
{
    public function __construct(private readonly Base_De_Donnees $_bdd)
    {
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
