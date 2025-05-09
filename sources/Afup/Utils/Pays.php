<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

/**
 * Classe de gestion des pays
 */
class Pays
{
    const DEFAULT_ID = 'FR';

    public function __construct(private readonly Base_De_Donnees $_bdd)
    {
    }

    /**
     * Renvoit un tableau associatif des pays avec le code ISO comme clé et le nom comme valeur
     *
     * @return array
     */
    public function obtenirPays()
    {
        $requete = 'SELECT id, nom FROM afup_pays ORDER BY nom';
        return $this->_bdd->obtenirAssociatif($requete);
    }

    /**
     * Renvoit le nom du pays à partir du code ISO
     *
     * @param  string $id Identifiant ISO 2a du pays
     * @return string
     */
    public function obtenirNom($id)
    {
        $requete = 'SELECT nom FROM afup_pays WHERE id =' . $this->_bdd->echapper($id);
        ;
        return $this->_bdd->obtenirUn($requete);
    }

    public function obtenirZonesFrancaises()
    {
        $zonesFrancaises[0] = '--';
        $zonesFrancaises[1] = '01 - Ile de France';
        $zonesFrancaises[2] = '02 - Nord Ouest';
        $zonesFrancaises[3] = '03 - Nord Est';
        $zonesFrancaises[4] = '04 - Sud Est';
        $zonesFrancaises[5] = '05 - Sud Ouest';

        return $zonesFrancaises;
    }
}
