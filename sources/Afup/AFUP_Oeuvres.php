<?php

class AFUP_Oeuvres {
    public $details = array();

    protected $_bdd;
    protected $loggit;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
        $this->loggit = realpath(dirname(__FILE__).'/../../htdocs/cache/robots/oeuvres') . '/loggit.csv';
        if (!file_exists($this->loggit)) {
            file_put_contents($this->loggit, "");
        }
        $this->loggit = realpath($this->loggit);
    }

    function calculer()
    {
        $this->rafraichirLogGit();
        $this->extraireOeuvresDepuisLogGit($this->loggit);
        $this->extraireOeuvresDepuisLogs();
        $this->extraireOeuvresDepuisPlanete();

        return $this->inserer();
    }

    function extraireOeuvresDepuisLogGit($loggit = null)
    {
        if (!file_exists($loggit) && !$loggit == null ) {
            return false;
        }
        if ($loggit == null) $loggit = $this->$loggit;

        $fp = fopen($this->loggit, 'r');
        while (($data = fgetcsv($fp, 1000, ";")) !== false) {
            $date = strtotime($data[3]);
            $date = mktime(0, 0, 0, date("m", $date), 1, date("Y", $date));
            $auteur = $data[2];
            if (!isset($auteurs[$auteur])) {
                $personnes_physiques = new AFUP_Personnes_Physiques($this->_bdd);
                $infosUser = $personnes_physiques->getUserByEmail($auteur);
                if ($infosUser) {
                    $auteurs[$auteur] = $infosUser['id'];
                }
            }
            if (isset($auteurs[$auteur])) { // on affiche que les membres AFUP
                $id_personne_physique = $auteurs[$auteur];
                if (!isset($this->details['git'][$id_personne_physique][$date])) {
                    $this->details['git'][$id_personne_physique][$date] = 0;
                }
                $this->details['git'][$id_personne_physique][$date]++;
            }
        }
        fclose($fp);
        return true;
    }

    function extraireLogSVNBrut($refresh = false)
    {
        if ($refresh) $this->rafraichirLogSVN();
        $logsvn = $this->logsvn;
        $xml = simplexml_load_file($logsvn);
        $revision = array();
        foreach ($xml->logentry as $logentry) {
            $current_rev = intval($logentry->attributes()->revision);
            $revision[$current_rev]["rev"] = $current_rev;
            $revision[$current_rev]["user"] = $logentry->author;
            $revision[$current_rev]["msg"] = $logentry->msg;
            $revision[$current_rev]["date"] = $logentry->date;
            if (isset($logentry->paths)) {
                foreach ($logentry->paths->children() as $path) {
                    $action = $path->attributes()->action;
                    $revision[$current_rev]["paths"][] = $action . " - " . $path;
                }
            }
        }
        arsort($revision);
        return $revision;
    }

    function rafraichirLogGit()
    {
        $date = mktime(0, 0, 0, date('m') - 12, 1, date('Y'));
        if (is_writable($this->loggit)) {
            chdir($GLOBALS['conf']->obtenir('git|local_repo'));
            $commande = 'git log --since="' . date('Y-m-d', $date) . '" --pretty="%H;%an;%ae;%ai;%s" | grep -v "Merge pull request" > '.$this->loggit;
            return exec($commande);
        } else {
            return false;
        }
    }

    function extraireLogGitBrut($refresh = false)
    {
        if ($refresh) $this->rafraichirLogGit();
        $fp = fopen($this->loggit, 'r');
        $revision = array();
        while (($data = fgetcsv($fp, 1000, ";")) !== false) {
            $current_rev = $data[0];
            $revision[$current_rev]["rev"] = $current_rev;
            $revision[$current_rev]["user"] = $data[1];
            $revision[$current_rev]["user_email"] = $data[2];
            $revision[$current_rev]["msg"] = $data[4];
            $revision[$current_rev]["date"] = $data[3];
        }
        fclose($fp);
        return $revision;
    }

    function extraireOeuvresDepuisLogs()
    {
        $requete = '
        	SELECT *
        	FROM afup_logs
        	WHERE date >= '.mktime(0, 0, 0, date('m') - 11, 1, date('Y')).'
        	AND date <= '.time().'
        	AND id_personne_physique > 0
        ';

        $oeuvres = $this->_bdd->obtenirTous($requete);
        if (is_array($oeuvres)) {
	        foreach ($oeuvres as $oeuvre) {
	            $id_personne_physique = $oeuvre['id_personne_physique'];
	            $date = mktime(0, 0, 0, date('m', $oeuvre['date']), 1, date('Y', $oeuvre['date']));
	            if (!isset($this->details['logs'][$id_personne_physique][$date])) {
	                $this->details['logs'][$id_personne_physique][$date] = 0;
	            }
	            $this->details['logs'][$id_personne_physique][$date]++;
	        }

	        return true;
        }

        return false;
    }

    function extraireOeuvresDepuisPlanete()
    {
        $requete = '
        	SELECT
        		pb.maj as date,
        		pf.id_personne_physique
        	FROM afup_planete_billet AS pb
        	INNER JOIN afup_planete_flux AS pf
        	ON pf.id = pb.afup_planete_flux_id
        	WHERE pb.maj >= '.mktime(0, 0, 0, date('m') - 11, 1, date('Y')).'
        	AND pb.maj <= '.time().'
        	AND pf.id_personne_physique > 0
        	AND pb.etat = ' . AFUP_PLANETE_BILLET_PERTINENT
        ;

        $oeuvres = $this->_bdd->obtenirTous($requete);
        if (is_array($oeuvres)) {
	        foreach ($oeuvres as $oeuvre) {
	            $id_personne_physique = $oeuvre['id_personne_physique'];
	            $date = mktime(0, 0, 0, date('m', $oeuvre['date']), 1, date('Y', $oeuvre['date']));
	            if (!isset($this->details['planete'][$id_personne_physique][$date])) {
	                $this->details['planete'][$id_personne_physique][$date] = 0;
	            }
	            $this->details['planete'][$id_personne_physique][$date]++;
	        }

	        return true;
        }

        return false;
    }

    function inserer()
    {
        foreach ($this->details as $categorie => $details_avec_id_personne_physique) {
            foreach ($details_avec_id_personne_physique as $id_personne_physique => $details_avec_date) {
                foreach ($details_avec_date as $date => $valeur) {
                    $this->insererDetail($categorie, $id_personne_physique, $date, $valeur);
                }
            }
        }

        return true;
    }

    function insererDetail($categorie, $id_personne_physique, $date, $valeur)
    {
		$requete = '
			INSERT INTO afup_oeuvres
			SET
			categorie = '.$this->_bdd->echapper($categorie).',
			id_personne_physique = '.$this->_bdd->echapper($id_personne_physique).',
			date = '.$this->_bdd->echapper($date).',
			valeur = '.$this->_bdd->echapper($valeur)
		;

		return $this->_bdd->executer($requete);
    }

    function obtenirOeuvresSur12Mois($id_personne_physique = null, $categorie = null)
    {
        $oeuvresSur12Mois = array();

        $requete = '
        	SELECT *
        	FROM afup_oeuvres
        	WHERE date >= '.mktime(0, 0, 0, date('m') - 11, 1, date('Y')).'
        	AND date <= '.time()
        ;

				if($categorie) {
					$requete .= '
					 AND categorie = "' . $categorie . '"';
				}
        if ($id_personne_physique) {
            switch (true) {
                case is_numeric($id_personne_physique):
		            $id_personne_physique = array($id_personne_physique);
                case is_array($id_personne_physique):
                    $requete .= '
		            	AND id_personne_physique IN ('.join(', ', $id_personne_physique).')'
		            ;
		            foreach ($id_personne_physique as $id) {
		                $oeuvresSur12Mois[$id] = array();
		            }
                    break;
            }
        }

        $oeuvres = $this->_bdd->obtenirTous($requete);
        if (is_array($oeuvres)) {
	        foreach ($oeuvres as $oeuvre) {
	            $date = mktime(0, 0, 0, date('m', $oeuvre['date']), 1, date('Y', $oeuvre['date']));
	            $oeuvresSur12Mois[$oeuvre['id_personne_physique']][$oeuvre['categorie']][$date] = $oeuvre['valeur'];
	        }
        }

        return $oeuvresSur12Mois;
    }

    function obtenirSparklinePersonnelleSur12Mois($id_personne_physique)
    {
        $sparkline = $this->obtenirSparklinesSur12Mois($id_personne_physique);

        return $sparkline[$id_personne_physique];
    }

    function obtenirSparklinesSur12Mois($id_personne_physique = null, $categorie = null)
    {
        $liste_mois = array();
        $debut = mktime(0, 0, 0, date('m') - 11, 1, date('Y'));
        $fin = mktime(0, 0, 0, date('m'), 1, date('Y'));
        while ($debut <= $fin) {
            $liste_mois[$debut] = 0;
            $debut = strtotime('+1 month', $debut);
        }

        $sparklinesSur12Mois = array();
        if ($id_personne_physique) {
            switch (true) {
                case is_numeric($id_personne_physique):
		            $id_personne_physique = array($id_personne_physique);
                case is_array($id_personne_physique):
		            foreach ($id_personne_physique as $id) {
		                $sparklinesSur12Mois[$id] = array();
		            }
                    break;
            }
        }

        $oeuvresSur12Mois = $this->obtenirOeuvresSur12Mois($id_personne_physique, $categorie);
        foreach ($oeuvresSur12Mois as $id_personne_physique => $categorieAvecOeuvres) {
            foreach ($categorieAvecOeuvres as $categorie => $datesAvecOeuvres) {
                $sparkline = $liste_mois;
                foreach ($datesAvecOeuvres as $date => $valeur) {
                    $sparkline[$date] = $valeur;
                }
                $sparklinesSur12Mois[$id_personne_physique][$categorie]['liste'] = join(',', $sparkline);
                $sparklinesSur12Mois[$id_personne_physique][$categorie]['minimum'] = min($sparkline);
                $sparklinesSur12Mois[$id_personne_physique][$categorie]['maximum'] = max($sparkline);
                $sparklinesSur12Mois[$id_personne_physique][$categorie]['dernier'] = array_pop($sparkline);
            }
        }

        return $sparklinesSur12Mois;
    }

    function obtenirSparklinesParCategorieDes12DerniersMois($id_personne_physique, $categorie = null)
    {
        $sparklinesParCategorieSur12Mois = array();
        $sparklinesSur12Mois = $this->obtenirSparklinesSur12Mois($id_personne_physique, $categorie);

        foreach ($sparklinesSur12Mois as $id => $oeuvresAvecCategorieSur12Mois) {
            foreach ($oeuvresAvecCategorieSur12Mois as $categorie => $oeuvre) {
                 $sparklinesParCategorieSur12Mois[$categorie][$id] = $oeuvre;
            }
        }

        return $sparklinesParCategorieSur12Mois;
    }

    function obtenirPersonnesPhysiquesLesPlusActives($categorie = null)
    {
				$categorie_sql = null;
				if($categorie !== null) {
					$categorie_sql = " AND categorie = '" .$categorie . "'";
				}

        $requete = '
        	SELECT id_personne_physique, categorie,
        	SUM(valeur) as compte
        	FROM afup_oeuvres
        	WHERE date >= '.mktime(0, 0, 0, date('m') - 11, 1, date('Y')).'
        	AND date <= '.time().
					$categorie_sql .'
            AND id_personne_physique != 0
        	GROUP BY id_personne_physique, categorie
        	ORDER BY compte DESC'
        ;

        $id_personnes_physiques = array();
        $oeuvres_comptes = $this->_bdd->obtenirTous($requete);
        foreach ($oeuvres_comptes as $oeuvre) {
            if (!isset($categories[$oeuvre['categorie']])) {
                $categories[$oeuvre['categorie']] = 0;
            }
            $categories[$oeuvre['categorie']]++;
            if ($categories[$oeuvre['categorie']] <= 8) {
                $id_personnes_physiques[] = $oeuvre['id_personne_physique'];
            }
        }

        return array_unique($id_personnes_physiques);
    }

		function obtenirCategories()
    {
        $requete = '
        	SELECT *,
        	categorie
        	FROM afup_oeuvres
        	WHERE date >= '.mktime(0, 0, 0, date('m') - 11, 1, date('Y')).'
        	AND date <= '.time().'
        	GROUP BY categorie
					'
        ;

        $liste_categories = $this->_bdd->obtenirTous($requete);
        $categories = array();
        foreach ($liste_categories as $unique_categorie) {
            $categories[] = $unique_categorie["categorie"];
        }

        return array_unique($categories);
    }
}
