<?php

declare(strict_types=1);

namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;

class Feuille
{
    const ID_FEUILLE_ANTENNES = 71;
    const ID_FEUILLE_ASSOCIATION = 74;
    const ID_FEUILLE_COLONNE_DROITE = 1;
    const ID_FEUILLE_HEADER = 21;
    const ID_FEUILLE_FOOTER = 38;
    const ID_FEUILLE_NOS_ACTIONS = 96;
    public $id_parent;
    public $nom;
    public $lien;
    public $alt;
    public $image;
    public $image_alt;
    public $position;
    public $date;
    public $etat;
    public $patterns;

    /**
     * @var Base_De_Donnees
     */
    protected $bdd;

    public function __construct(
        public $id = 0,
        $bdd = false,
    ) {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();
    }

    public function inserer()
    {
        if ($this->id > 0) {
            $this->supprimer();
        }
        $requete = 'INSERT INTO afup_site_feuille
        			SET
        			id_parent = ' . $this->bdd->echapper($this->id_parent ?: null) . ',
        			nom       = ' . $this->bdd->echapper($this->nom) . ',
        			lien      = ' . $this->bdd->echapper($this->lien) . ',
        			alt       = ' . $this->bdd->echapper($this->alt) . ',
        			image     = ' . $this->bdd->echapper($this->image) . ',
                    image_alt = ' . $this->bdd->echapper($this->image_alt) . ',
        			position  = ' . $this->bdd->echapper($this->position) . ',
        			date      = ' . $this->bdd->echapper($this->date) . ',
        			patterns  = ' . $this->bdd->echapper($this->patterns) . ',
        			etat    = ' . $this->bdd->echapper($this->etat);
        if ($this->id > 0) {
            $requete .= ', id = ' . $this->bdd->echapper($this->id);
        }

        return $this->bdd->executer($requete);
    }

    public function modifier()
    {
        $requete = 'UPDATE afup_site_feuille
        			SET
        			id_parent = ' . $this->bdd->echapper($this->id_parent ?: null) . ',
        			nom       = ' . $this->bdd->echapper($this->nom) . ',
        			lien      = ' . $this->bdd->echapper($this->lien) . ',
        			alt       = ' . $this->bdd->echapper($this->alt) . ',
        			image     = ' . $this->bdd->echapper($this->image) . ',
        			image_alt = ' . $this->bdd->echapper($this->image_alt) . ',
                    position  = ' . $this->bdd->echapper($this->position) . ',
        			date      = ' . $this->bdd->echapper($this->date) . ',
        			patterns  = ' . $this->bdd->echapper($this->patterns) . ',
        			etat      = ' . $this->bdd->echapper($this->etat) . '
        			WHERE id  = ' . (int) $this->id;

        return $this->bdd->executer($requete);
    }

    public function remplir(array $f): void
    {
        $this->id = $f['id'];
        $this->id_parent = $f['id_parent'];
        $this->nom = $f['nom'];
        $this->lien = $f['lien'];
        $this->alt = $f['alt'];
        $this->image = $f['image'];
        $this->image_alt = $f['image_alt'];
        $this->position = $f['position'];
        $this->date = $f['date'];
        $this->etat = $f['etat'];
        $this->patterns = $f['patterns'];
    }

    public function exportable(): array
    {
        return [
            'id' => $this->id,
            'id_parent' => $this->id_parent,
            'nom' => $this->nom,
            'lien' => $this->lien,
            'alt' => $this->alt,
            'image' => $this->image,
            'image_alt' => $this->image_alt,
            'position' => $this->position,
            'date' => date('Y-m-d', (int) $this->date),
            'etat' => $this->etat,
            'patterns' => $this->patterns,
        ];
    }

    public function charger(): void
    {
        $requete = 'SELECT *
                    FROM afup_site_feuille
                    WHERE id = ' . $this->bdd->echapper($this->id);
        $f = $this->bdd->obtenirEnregistrement($requete);
        $this->remplir($f);
    }

    public function supprimer()
    {
        $requete = 'DELETE FROM afup_site_feuille WHERE id = ' . $this->bdd->echapper($this->id);
        return $this->bdd->executer($requete);
    }

    /**
     * @return int[]
     */
    public function positionable(): array
    {
        $positions = [];
        for ($i = 9; $i >= -9; $i--) {
            $positions[$i] = $i;
        }
        return $positions;
    }
}
