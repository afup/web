<?php

namespace Afup\Site;
class Tags
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     \Afup\Site\Utils\Base_De_Donnees
     * @access  private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    function extraireTags($chaine)
    {
        $regex = <<<HERE
/  "  ( (?:[^"\\\\]++|\\\\.)*+ ) \"
| '  ( (?:[^'\\\\]++|\\\\.)*+ ) \'
| \( ( [^)]*                  ) \)
| [\s,]+
/x
HERE;

        return preg_split($regex, $chaine, -1,
            PREG_SPLIT_NO_EMPTY
            | PREG_SPLIT_DELIM_CAPTURE);
    }

    function obtenirTagsSurPersonnePhysique($id_personne_physique, $champs = '*', $order = 'date DESC', $associatif = false)
    {
        $where = ' AND source = "afup_personnes_physiques" ';
        $where .= ' AND id_source = ' . $this->_bdd->echapper($id_personne_physique);
        return $this->obtenirListe($champs, $order, $associatif, $where);
    }

    /**
     *
     * @deprecated Use obtenirPersonnesPhysiquesTagues()
     */
    function obtenirPersonnesPhysisquesTagues($tag)
    {
        return $this->obtenirPersonnesPhysiquesTagues($tag);
    }

    function obtenirPersonnesPhysiquesTagues($tag)
    {
        $requete = ' SELECT ';
        $requete .= '  t.*, ';
        $requete .= '  p.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_personnes_physiques p ';
        $requete .= ' INNER JOIN ';
        $requete .= '  afup_tags t ';
        $requete .= ' ON ';
        $requete .= '  t.source = \'afup_personnes_physiques\' ';
        $requete .= ' AND ';
        $requete .= '  t.id_source = p.id ';
        $requete .= ' WHERE ';
        $requete .= '  t.tag = ' . $this->_bdd->echapper($tag);

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListeUnique($champs = '*')
    {
        $tags = $this->obtenirListe($champs);
        $tags_uniques = array();
        foreach ($tags as $tag) {
            if (!isset($tags_uniques[$tag['tag']])) {
                $tags_uniques[$tag['tag']] = $tag;
            }
        }

        return $tags_uniques;
    }

    function obtenirNoeudsTags()
    {
        $requete = ' SELECT';
        $requete .= '  tag as noeud, ';
        $requete .= '  id_source as lien ';
        $requete .= ' FROM';
        $requete .= '  afup_tags ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirNoeudsPersonnesPhysiques()
    {
        $requete = ' SELECT ';
        $requete .= '  pp.login as noeud, ';
        $requete .= '  t.tag as lien ';
        $requete .= ' FROM ';
        $requete .= '  afup_personnes_physiques pp ';
        $requete .= ' INNER JOIN ';
        $requete .= '  afup_tags t ';
        $requete .= ' ON ';
        $requete .= '  pp.id = t.id_source ';
        $requete .= ' AND ';
        $requete .= '  t.source = \'afup_personnes_physiques\'';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListe($champs = '*',
                          $ordre = 'date DESC',
                          $associatif = false,
                          $where = '')
    {
        $requete = ' SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM';
        $requete .= '  afup_tags ';
        $requete .= ' WHERE 1 = 1 ';
        $requete .= $where;
        $requete .= ' ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function supprimer($id)
    {
        $requete = 'DELETE FROM afup_tags WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }

    function supprimerParPersonnesPhysiques($id)
    {
        $requete = 'DELETE FROM afup_tags WHERE id_personne_physique=' . $id;
        return $this->_bdd->executer($requete);
    }

    function enregistrerTags($formulaire, $id_personne_physique, $date)
    {
        $ok = true;
        $tags = $this->extraireTags($formulaire->exportValue('tag'));
        foreach ($tags as $tag) {
            $ok += (bool)$this->enregistrer($formulaire->exportValue('source'),
                $formulaire->exportValue('id_source'),
                $tag,
                $id_personne_physique,
                $date,
                $formulaire->exportValue('id'));
        }

        return $ok;
    }

    function enregistrer($source, $id_source, $tag, $id_personne_physique, $date, $id)
    {
        if ($id > 0) {
            $requete = ' UPDATE afup_tags ';
        } else {
            $requete = ' INSERT INTO afup_tags ';
        }
        $requete .= ' SET ';
        $requete .= ' source = ' . $this->_bdd->echapper($source) . ',';
        $requete .= ' id_source = ' . $this->_bdd->echapper($id_source) . ',';
        $requete .= ' tag = ' . $this->_bdd->echapper($tag, true) . ',';
        $requete .= ' id_personne_physique = ' . $this->_bdd->echapper($id_personne_physique) . ',';
        $requete .= ' date = ' . $this->_bdd->echapper($date);
        if ($id > 0) {
            $requete .= ' WHERE id = ' . $id;
        }

        return $this->_bdd->executer($requete);
    }
}
