<?php

declare(strict_types=1);

namespace Afup\Site\Forum;

use Afup\Site\Utils\Base_De_Donnees;

class Partenaires
{
    public function __construct(private readonly Base_De_Donnees $_bdd)
    {
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
        $requete .= '  afup_forum_partenaires ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public function obtenirListe(string $ordre = 'id_forum desc, id_niveau_partenariat asc, ranking asc', $associatif = false)
    {
        $requete = 'SELECT';
        $requete .= '  afup_forum_partenaires.*, afup_forum.path, afup_forum.titre as nom_forum, afup_niveau_partenariat.titre as niveau_partenariat';
        $requete .= ' FROM';
        $requete .= '  afup_forum_partenaires';
        $requete .= ' INNER JOIN';
        $requete .= '  afup_forum ON afup_forum.id=afup_forum_partenaires.id_forum';
        $requete .= ' INNER JOIN';
        $requete .= '  afup_niveau_partenariat ON afup_forum_partenaires.id_niveau_partenariat=afup_niveau_partenariat.id';
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    /**
     * @return non-empty-list[]
     */
    public function obtenirTousPartenairesForum(string $id_forum): array
    {
        $requete = 'SELECT';
        $requete .= '  *, afup_forum_partenaires.id as partenaire_id  ';
        $requete .= 'FROM';
        $requete .= '  afup_forum_partenaires ';
        $requete .= 'INNER JOIN';
        $requete .= '  afup_niveau_partenariat ON afup_niveau_partenariat.id = afup_forum_partenaires.id_niveau_partenariat ';
        $requete .= 'WHERE';
        $requete .= ' id_forum=' . $id_forum . ' ';
        $requete .= 'ORDER BY';
        $requete .= ' id_niveau_partenariat, ranking';
        $partenaires = $this->_bdd->obtenirTous($requete);
        $parType = [];
        foreach ($partenaires as $p) {
            $parType[$p['titre']][] = $p;
        }
        return $parType;
    }

    public function ajouter($id_forum, $id_niveau_partenariat, $ranking,
                     $nom, $presentation, $site, $logo)
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_forum_partenaires (id, id_forum, id_niveau_partenariat, ranking,';
        $requete .= '  nom, presentation, site, logo) ';
        $requete .= 'VALUES (null,';
        $requete .= (int) $id_forum . ',';
        $requete .= (int) $id_niveau_partenariat . ',';
        $requete .= (int) $ranking . ',';
        $requete .= $this->_bdd->echapper($nom) . ',';
        $requete .= $this->_bdd->echapper($presentation) . ',';
        $requete .= $this->_bdd->echapper($site) . ',';
        $requete .= $this->_bdd->echapper($logo) . ')';
        //echo $requete;
        return $this->_bdd->executer($requete);
    }

    public function modifier(string $id, $id_forum, $id_niveau_partenariat, $ranking,
                      $nom, $presentation, $site, $logo)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_forum_partenaires ';
        $requete .= 'SET';
        $requete .= '  id_forum=' . (int) $id_forum . ',';
        $requete .= '  id_niveau_partenariat=' . (int) $id_niveau_partenariat . ',';
        $requete .= '  ranking=' . (int) $ranking . ',';
        $requete .= '  nom=' . $this->_bdd->echapper($nom) . ',';
        $requete .= '  presentation=' . $this->_bdd->echapper($presentation) . ',';
        $requete .= '  site=' . $this->_bdd->echapper($site) . ',';
        $requete .= '  logo=' . $this->_bdd->echapper($logo) . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;

        return $this->_bdd->executer($requete);
    }

    public function supprimer(string $id)
    {
        $requete = 'DELETE FROM afup_forum_partenaires WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }
}
