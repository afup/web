<?php

class AFUP_Tags
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     object
     * @access  private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param  object    $bdd   Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    function preparerFichierDot($elements=array())
    {
        $dot  = "graph G {\n";

        $noeuds_par_lien = array();
        foreach ($elements as $element) {
            $element['noeud'] = strtolower($element['noeud']);
            $element['noeud'] = str_replace(".", "", $element['noeud']);
            $element['noeud'] = str_replace(" ", "", $element['noeud']);
            if (!empty($element['noeud']) and !preg_match("/^[0-9]/", $element['noeud'])) {
                $noeuds_par_lien[$element['lien']][] = $element['noeud'];
            }
        }

        $dots = array();
        foreach ($noeuds_par_lien as $lien => $noeuds) {
            while (sizeof($noeuds) > 0) {
                $first_noeud = array_shift($noeuds);
                foreach ($noeuds as $noeud) {
                    if ($noeud != $first_noeud) {
	                    $line = "  ".$first_noeud." -- ".$noeud.";\n";
	                    $line_alternative = "  ".$noeud." -- ".$first_noeud.";\n";
	                    if (!in_array($line, $dots) and !in_array($line_alternative, $dots)) {
	                        $dots[] = $line;
	                    }
                    }
                }
            }
        }
        $dot .= implode("", $dots);
        $dot .= "}\n";

        return $dot;
    }

    function extraireTags($chaine)
    {
        if (empty($chaine)) {
            return array();
        }

        if (substr_count($chaine, "'") % 2 != 0) {
            return array();
        }

        if (strpos($chaine, "'") === false) {
            return explode(" ", $chaine);
        } else {
            $premier_guillemet = strpos($chaine, "'", 0);
            $deuxieme_guillemet = strpos($chaine, "'", 1);
            $tags = $this->extraireTags(substr($chaine, 0, $premier_guillemet));
            $tags[] = substr($chaine, $premier_guillemet + 1, $premier_guillemet + $deuxieme_guillemet - 1);
            $tags = array_merge($tags, $this->extraireTags(substr($chaine, $deuxieme_guillemet + 2)));

            return $tags;
        }
    }

    function obtenirTagsSurPersonnePhysique($id_personne_physique, $champs = '*', $order = 'date DESC', $associatif = false)
    {
        $where  = ' AND source = "afup_personnes_physiques" ';
        $where .= ' AND id_source = ' . $this->_bdd->echapper($id_personne_physique);
        return $this->obtenirListe($champs, $order, $associatif, $where);
    }

    /**
     *
     * @deprecated Use obtenirPersonnesPhysiquesTagues()
     */
    function obtenirPersonnesPhysisquesTagues($tag) {
    	return $this->obtenirPersonnesPhysiquesTagues($tag);
    }
    function obtenirPersonnesPhysiquesTagues($tag)
    {
        $requete  = ' SELECT ';
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
        $requete  = ' SELECT';
        $requete .= '  tag as noeud, ';
        $requete .= '  id_source as lien ';
        $requete .= ' FROM';
        $requete .= '  afup_tags ';

        return $this->_bdd->obtenirTous($requete);
    }

    /**
     *
     * @deprecated Use obtenirNoeudsPersonnesPhysiques()
     */
    function obtenirNoeudsPersonnesPhysiqyes() {
    	return $this->obtenirNoeudsPersonnesPhysiques();
    }
    function obtenirNoeudsPersonnesPhysiques()
    {
        $requete  = ' SELECT ';
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
        $requete  = ' SELECT';
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
                                            $this->_bdd->echapper($tag),
                                            $id_personne_physique,
                                            $date,
                                            $formulaire->exportValue('id'));
        }

        return $ok;
    }

    function enregistrer($source, $id_source, $tag, $id_personne_physique, $date, $id)
    {
		if ($id > 0) {
	        $requete  = ' UPDATE afup_tags ';
		} else {
	        $requete  = ' INSERT INTO afup_tags ';
		}
        $requete .= ' SET ';
        $requete .= ' source = '.$this->_bdd->echapper($source) . ',';
        $requete .= ' id_source = '.$this->_bdd->echapper($id_source) . ',';
        $requete .= ' tag = '.$this->_bdd->echapper($tag) . ',';
        $requete .= ' id_personne_physique = '.$this->_bdd->echapper($id_personne_physique) . ',';
        $requete .= ' date = '.$this->_bdd->echapper($date);
		if ($id > 0) {
	        $requete .= ' WHERE id = '.$id;
		}

		return $this->_bdd->executer($requete);
    }
}
