<?php
define('AFUP_PLANETE_BILLET_PERTINENT' , 1);
define('AFUP_PLANETE_BILLET_CREUX'     , 0);

class AFUP_Planete_Billet
{
    var $_bdd;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    function obtenirListe($champs     = '*',
                          $ordre      = 'nom',
                          $associatif = false,
                          $filtre     = false,
                          $limit      = false)
    {
        $requete  = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_planete_billet ';
        if ($filtre) {
            $requete .= 'WHERE nom LIKE \'%' . $filtre . '%\' ';
        }
        $requete .= 'ORDER BY ' . $ordre . ' ';
        if (is_numeric($limit)) {
            $requete .= 'LIMIT 0, ' . (int)$limit . ' ';
        }
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function obtenir($id, $champs = '*')
    {
        $requete  = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_planete_billet ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirIdDepuisClef($clef) {
        $requete  = 'SELECT';
        $requete .= '  id ';
        $requete .= 'FROM';
        $requete .= '  afup_planete_billet ';
        $requete .= 'WHERE clef= ' . $this->_bdd->echapper($clef);
        return $this->_bdd->obtenirUn($requete);
    }

    function sauvegarder($flux_id, $clef, $titre, $url, $maj, $auteur, $resume, $contenu, $etat)
    {
    	$id = $this->obtenirIdDepuisClef($clef);
    	if ($id > 0) {
    		$resultat = $this->modifier($id, $flux_id, $clef, $titre, $url, $maj, $auteur, $resume, $contenu, $etat);
    	} else {
    		$resultat = $this->ajouter($flux_id, $clef, $titre, $url, $maj, $auteur, $resume, $contenu, $etat);
    	}
    	return $resultat;
    }

    function ajouter($flux_id, $clef, $titre, $url, $maj, $auteur, $resume, $contenu, $etat)
    {
        $requete  = 'INSERT INTO ';
        $requete .= '  afup_planete_billet (afup_planete_flux_id, clef, titre, url, maj, auteur, resume, contenu, etat) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($flux_id)          . ',';
        $requete .= $this->_bdd->echapper($clef)             . ',';
        $requete .= $this->_bdd->echapper($titre)            . ',';
        $requete .= $this->_bdd->echapper($url)              . ',';
        $requete .= $this->_bdd->echapper($maj)              . ',';
        $requete .= $this->_bdd->echapper($auteur)           . ',';
        $requete .= $this->_bdd->echapper($resume)           . ',';
        $requete .= $this->_bdd->echapper($contenu)           . ',';
        $requete .= $this->_bdd->echapper($etat)             . ')';
        return $this->_bdd->executer($requete);

    }

    function modifier($id, $flux_id, $clef, $titre, $url, $maj, $auteur, $resume, $contenu, $etat)
    {
        $requete  = 'UPDATE ';
        $requete .= '  afup_planete_billet ';
        $requete .= 'SET';
        $requete .= '  afup_planete_flux_id='  . $this->_bdd->echapper($flux_id)  . ',';
        $requete .= '  clef='                  . $this->_bdd->echapper($clef)     . ',';
        $requete .= '  titre='                 . $this->_bdd->echapper($titre)    . ',';
        $requete .= '  url='                   . $this->_bdd->echapper($url)      . ',';
        $requete .= '  maj='                   . $this->_bdd->echapper($maj)      . ',';
        $requete .= '  auteur='                . $this->_bdd->echapper($auteur)   . ',';
        $requete .= '  resume='                . $this->_bdd->echapper($resume)   . ',';
        $requete .= '  contenu='               . $this->_bdd->echapper($contenu)  . ',';
        $requete .= '  etat='                  . $this->_bdd->echapper($etat)     . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;
        return $this->_bdd->executer($requete);
    }

    function supprimer($id)
    {
		$requete = 'DELETE FROM afup_planete_billet WHERE id=' . $id;
		return $this->_bdd->executer($requete);
    }

    function tronquerContenu($contenu, $url, $caracteres=3000) {
    	$contenu_tronque = $contenu;
    	$est_tronque = false;

    	if (strlen($contenu) > $caracteres) {
    		$dernier_point = strpos($contenu, ".", $caracteres);
    		if ($dernier_point) {
    			$est_tronque = true;
    			$contenu_tronque = substr($contenu, 0, $dernier_point + 1);
    		}
    	}

		require_once 'htmlpurifier/HTMLPurifier.auto.php';
	    $config = HTMLPurifier_Config::createDefault();
    	$config->set('Core', 'Encoding', 'UTF-8');
    	$config->set('HTML', 'Doctype', 'HTML 4.01 Transitional');
    	$purifier = new HTMLPurifier($config);
    	$contenu_tronque = $purifier->purify($contenu_tronque);

    	if ($est_tronque) {
    		$contenu_tronque .= "<p><a href=\"".$url."\">la suite...</a></p>";
    	}

    	return $contenu_tronque;
    }

	function obtenirDerniersBilletsTronques($page=0, $format=DATE_ATOM)
	{
		$billets_tronques = array();

		$billets = $this->obtenirDerniersBilletsComplets($page, $format);
		foreach ($billets as $billet) {
			$billet['contenu'] = $this->tronquerContenu($billet['contenu'], $billet['url']);
			$billets_tronques[] = $billet;
		}

		return $billets_tronques;
	}

    function obtenirDerniersBilletsComplets($page=0, $format=DATE_ATOM, $nombre=10)
    {
    	$requete  = 'SELECT ';
    	$requete .= '  afup_planete_billet.titre, ';
    	$requete .= '  afup_planete_billet.url, ';
    	$requete .= '  afup_planete_billet.maj, ';
    	$requete .= '  afup_planete_billet.auteur, ';
    	$requete .= '  afup_planete_billet.contenu, ';
    	$requete .= '  afup_planete_flux.nom as flux_nom, ';
    	$requete .= '  afup_planete_flux.url as flux_url ';
    	$requete .= 'FROM ';
    	$requete .= '  afup_planete_billet ';
    	$requete .= 'INNER JOIN ';
    	$requete .= '  afup_planete_flux ';
    	$requete .= 'ON ';
    	$requete .= '  afup_planete_flux.id = afup_planete_billet.afup_planete_flux_id ';
    	$requete .= 'WHERE ';
    	$requete .= '  afup_planete_billet.etat = '.AFUP_PLANETE_BILLET_PERTINENT.' ';
    	$requete .= 'ORDER BY ';
    	$requete .= '  afup_planete_billet.maj DESC ';
    	$requete .= 'LIMIT '.($page * 10).', '.(int)$nombre;

		$billets = $this->_bdd->obtenirTous($requete);
		foreach ($billets as &$billet) {
			$billet['maj'] = date($format, $billet['maj']);
		}

        return $billets;
    }

    function avecContenuPertinent($contenu)
    {
		require_once 'Afup/AFUP_Configuration.php';
		$conf = $GLOBALS['AFUP_CONF'];

    	$pertinent = AFUP_PLANETE_BILLET_CREUX;

    	$contenu = strip_tags($contenu);
    	if (preg_match("/".$conf->obtenir('planete|pertinence')."/i", $contenu)) {
    		$pertinent = AFUP_PLANETE_BILLET_PERTINENT;
    	}

    	return $pertinent;
    }
}

?>