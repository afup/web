<?php
class AFUP_Forum
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
    function AFUP_Forum(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant un forum
     *
     * @param  int      $id         Identifiant du forum
     * @param  string   $champs     Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenir($id, $champs = '*')
    {
        $requete  = 'SELECT';
        $requete .= '  ' . $champs . ', annee as forum_annee ';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function supprimable($id) {
        $requete  = 'SELECT';
        $requete .= '  f.id, count(session_id) as sessions,count(i.id) as inscriptions ';
        $requete .= 'FROM';
        $requete .= '  afup_forum f ';
        $requete .= 'LEFT JOIN afup_sessions s ON (f.id = s.id_forum) ';
        $requete .= 'LEFT JOIN afup_inscription_forum i ON (f.id = i.id_forum) ';
        $requete .= 'WHERE f.id=' . $id;

        $forum = $this->_bdd->obtenirEnregistrement($requete);

        return $forum['sessions'] == 0 && $forum['inscriptions'] == 0;
    }

    function obtenirNombrePlaces($id=NULL) {
      if (empty($id)) {
        $id = $this->obtenirDernier();
      }
      $enregistrement = $this->obtenir($id, 'nb_places');

      return  $enregistrement['nb_places'];
    }

    function obtenirDebut($id_forum) {
        $requete  = 'SELECT UNIX_TIMESTAMP(date_debut)';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
        $requete .= 'WHERE';
        $requete .= '  id =  '.(int)$id_forum;
        return $this->_bdd->obtenirUn($requete);
    }

    function obtenirPrecedent($id_forum) {
        $requete  = 'SELECT MAX(id)';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
        $requete .= 'WHERE';
        $requete .= '  id <  '.(int)$id_forum;
        return $this->_bdd->obtenirUn($requete);
    }

    function obtenirDernier()
    {
        $requete  = 'SELECT id ';
        $requete .= 'FROM afup_forum ';
        $requete .= 'ORDER BY date_debut desc';
        return $this->_bdd->obtenirUn($requete);
    }

    /**
     * Renvoit la liste des inscriptions à facturer ou facturé au forum
     *
     * @param  string   $champs         Champs à renvoyer
     * @param  string   $ordre          Tri des enregistrements
     * @param  bool     $associatif     Renvoyer un tableau associatif ?
     * @access public
     * @return array
     */
    function obtenirListe($id_forum   = null,
                          $champs     = '*',
                          $ordre      = 'titre',
                          $associatif = false,
                          $filtre     = false)
    {
        $requete  = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
        $requete .= 'ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function afficherDeroulementMobile($sessions) {
    	$deroulement = "<div class=\"deroulements\">";
    	$jour = 0;
    	$heure = 0;
    	foreach ($sessions as $session) {
    		if ($jour != mktime(0, 0, 0, date("m", $session['debut']), date("d", $session['debut']), date("Y", $session['debut']))) {
    			$jour = mktime(0, 0, 0, date("m", $session['debut']), date("d", $session['debut']), date("Y", $session['debut']));
    			$deroulement .= "<h2 class=\"jour\">".($jour>10000 ? date("d/m/Y", $jour) : 'Jour à définir')."</h2>";
    		}
    		if ($heure != $session['debut']) {
    			$heure = $session['debut'];
    			$deroulement .= "<h3 class=\"horaire\">".date("H\hi", $heure)."</h3>";
    		}

    		$classes = array("deroulement");
    		$classes[] = $session['journee'];
			if ($session['keynote'] == 1) {
    			$classes[] = "keynote";
			}

    		$conferenciers = $session['conf1'];
    		if (!empty($session['conf2'])) {
    			$conferenciers .= "<br />".$session['conf2'];
    		}

    		$deroulement .= "<div class=\"".join(" ", $classes)."\">";
    		$deroulement .= "    <div class=\"session\"><a href=\"sessions.php#".$session['session_id']."\">".$session['titre']."</a></div>";
    		$deroulement .= "    <div class=\"conferenciers\">".$conferenciers."</div>";
    		$deroulement .= "    <div class=\"salle\">".$session['nom_salle']."</div>";
    		$deroulement .= "</div>";
    	}
    	$deroulement .= "</div>";

    	return $deroulement;
    }

    function afficherDeroulement($sessions) {
    	$deroulement = "<div class=\"deroulements\">";
    	$jour = 0;
    	$heure = 0;
    	foreach ($sessions as $session) {
    		if ($jour != mktime(0, 0, 0, date("m", $session['debut']), date("d", $session['debut']), date("Y", $session['debut']))) {
    			$jour = mktime(0, 0, 0, date("m", $session['debut']), date("d", $session['debut']), date("Y", $session['debut']));
    			$deroulement .= "<h2 class=\"jour\">" . ($jour>10000 ? date("d/m/Y", $jour) : 'Jour à définir') ."</h2>";
    		}
    		if ($heure != $session['debut']) {
    			$heure = $session['debut'];
    			$deroulement .= "<h3 class=\"horaire\">".date("H\hi", $heure)."</h3>";
    		}

    		$classes = array("deroulement");
    		$classes[] = $session['journee'];
			if ($session['keynote'] == 1) {
    			$classes[] = "keynote";
			}

    		$conferenciers = $session['conf1'];
    		if (!empty($session['conf2'])) {
    			$conferenciers .= "<br />".$session['conf2'];
    		}

    		$deroulement .= "<div class=\"".join(" ", $classes)."\">";
    		$deroulement .= "    <div class=\"session\"><a href=\"sessions.php#".$session['session_id']."\">".$session['titre']."</a></div>";
    		$deroulement .= "    <div class=\"conferenciers\">".$conferenciers."</div>";
    		$deroulement .= "</div>";
    	}
    	$deroulement .= "</div>";

    	return $deroulement;
    }

    function afficherAgenda($sessions) {
    	$slots = array();
    	$salles = array();
    	foreach ($sessions as $session) {
    		$jour = mktime(0, 0, 0, date("m", $session['debut']), date("d", $session['debut']), date("Y", $session['debut']));
    		$slots[$jour][$session['nom_salle']][$session['debut']] = $session;
    		if (!isset($debuts[$jour])) {
    			$debuts[$jour] = $session['debut'];
    		} else {
	    		$debuts[$jour] = min($session['debut'], $debuts[$jour]);
    		}
    		$salles[] = $session['id_salle'];
    	}
    	$salles = array_unique($salles);
    	sort($salles);
    	$salles = array_flip($salles);

    	$agenda = "";
    	$passage_jour = 0;
    	foreach ($slots as $jour => $slots_avec_salle) {
    		$nb_salles = count($slots_avec_salle);
    		$agenda .= "<div class=\"slots\" style=\"height: 1700px;\">";
    		$agenda .= "<h2 style=\"position: absolute; width: 100%; top: ".round($passage_jour * 1600)."px;\">".date("d/m/Y", $jour)."</h2>";
    		foreach ($slots_avec_salle as $salle => $slots_avec_horaire) {
    			foreach ($slots_avec_horaire as $debut => $session) {
    				$classes = array("slot");
    				$classes[] = $session['journee'];

    				$conferenciers = $session['conf1'];
    				if (!empty($session['conf2'])) {
    					$conferenciers .= "<br />".$session['conf2'];
    				}

    				$styles = array("position: absolute;");
    				if ($session['keynote'] == 1) {
    					$classes[] = "keynote";
    					$styles[] = "width: 100%;";
						$styles[] = "left: 0%;";
    				} else {
    					$styles[] = "width: ".round(100 / $nb_salles)."%;";
	    				$styles[] = "left: ".($salles[$session['id_salle']] * round(100 / $nb_salles))."%;";
    				}
    				$styles[] = "height: ".round(($session['fin'] - $session['debut']) / 19)."px;";
    				$styles[] = "top: ".round(40 + $passage_jour * 1600 + ($session['debut'] - $debuts[$jour]) / 19)."px;";

    				$agenda .= "<div class=\"".join(" ", $classes)."\" style=\"".join(" ", $styles)."\">";
    				$agenda .= "    <div class=\"session\"><a href=\"sessions.php#".$session['session_id']."\">".$session['titre']."</a></div>";
    				$agenda .= "    <div class=\"conferenciers\">".$conferenciers."</div>";
    				$agenda .= "    <div class=\"horaire\">".date("H\hi", $session['debut'])." - ".date("H\hi", $session['fin'])."</div>";
    				$agenda .= "</div>";
    			}
    		}
    		$agenda .= "</div>";
	    	$passage_jour++;
       	}

    	return $agenda;
    }

    /**
     * Récupérer l'agenda du forum.
     *
     * Pour une année donnée pass�e en paramètre, retourne
     * les informations nécessaires à la construction du tableau
     * de l'agenda du forum AFUP correspondant.
     *
     * @param Int $annee (Optionnel, retournera tout si aucunne année indiquée)
     */
    function obtenirAgenda($annee = null, $forum_id = null)
    {
        $sWhere = array();
        if(isset($annee))
        {
            $tdebut = mktime(0,0,0,1,1,$annee);
            $tfin   = mktime(0,0,0,1,1,($annee + 1));
            $aWhere[] = "p.debut >= ". $tdebut;
            $aWhere[] = "p.fin < ". $tfin;
            $aWhere[] = "s.plannifie = 1";
        }

        if (null !== $forum_id)
        {
            $aWhere[] = "l.id_forum = ".$forum_id;
        }

        $sWhere = "WHERE ". implode(" AND ", $aWhere);
        $requete  = "SELECT ".
                     " ( SELECT CONCAT(c.nom,' ', c.prenom , ' - ', c.societe )  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = s.session_id order by c.conferencier_id asc limit 1) as conf1 ,
                      ( SELECT CONCAT(c.nom,' ', c.prenom, ' - ', c.societe)  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = s.session_id order by c.conferencier_id asc limit 1,1) as conf2 , ".

                    "    s.session_id, s.titre, s.journee, ".
                    "    FROM_UNIXTIME(p.debut, '%d-%m-%Y') AS 'jour', ".
                    "    FROM_UNIXTIME(p.debut, '%H:%i') AS 'debut', ".
                    "    FROM_UNIXTIME(p.fin, '%H:%i') AS 'fin', ".
                    "    p.id_salle, ".
                    "    p.keynote, ".
                    "    l.nom ".
                    "FROM   afup_sessions       s ".
                    "  JOIN afup_forum_planning p ON s.session_id = p.id_session ".
                    "  JOIN afup_forum_salle    l ON p.id_salle   = l.id ".
                    $sWhere ." ".
                    "ORDER BY p.debut ASC, p.id_salle ASC";
        $planning = $this->_bdd->obtenirTous($requete);
        return $planning;
    }

   function envoyeMailVotePlanning() {

        $sujet = 'Consultation des membres Forum PHP 2009';

        $corps ="Bonjour, \n";
        $corps .="La liste des sessions du forum PHP a été mise en ligne la semaine dernière. Il nous reste à finaliser la programmation de ces sessions (horaires et salles). \n";
        $corps .="Merci de venir noter chacune de ces sessions sur le site de l'AFUP (cf. lien ci-dessous) ; cela nous permettra d'évaluer les audiences attendues et d'adapter au mieux notre programmation. \n\n";
        $corps .="Le vote est anonyme, non modifiable et le résultat restera secret  \n";
        $corps .="    \n";

        $requete  = 'SELECT';
        $requete .= '  afup_personnes_physiques.id, ';
        $requete .= '  afup_personnes_physiques.email, ';
        $requete .= '  afup_personnes_physiques.login, ';
        $requete .= '  CONCAT(afup_personnes_physiques.nom, \' \', afup_personnes_physiques.prenom) as nom ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE etat=1 limit 1';
        $personnes_physiques = $this->_bdd->obtenirTous($requete);


        $succes = false;
        require_once 'phpmailer/class.phpmailer.php';
        foreach ($personnes_physiques as $personne_physique) {
            $hash = md5($personne_physique['id'] . '_' . $personne_physique['email'] . '_' . $personne_physique['login']);
            $link = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?hash='.$hash;
            $link .= '&action=forum_planning_vote';
            var_dump($corps.$link);die();
            $mail = new PHPMailer;
            if ($GLOBALS['conf']->obtenir('mails|serveur_smtp')) {
                $mail->IsSMTP();
                $mail->Host = $GLOBALS['conf']->obtenir('mails|serveur_smtp');
                $mail->SMTPAuth = false;
            }
            $personne_physique['email'] = 'xgorse@elao.com';
            $mail->AddAddress($personne_physique['email'], $personne_physique['nom']);
            $mail->From     = $GLOBALS['conf']->obtenir('mails|email_expediteur');
            $mail->FromName = $GLOBALS['conf']->obtenir('mails|nom_expediteur');
            $mail->BCC      = $GLOBALS['conf']->obtenir('mails|email_expediteur');
            $mail->Subject  = $sujet;
            $mail->Body     = $corps . $link;

           // $mail->Send();
            $succes += 1;
        }

        return $succes;
    }

    /**
     * Compte en nombre de demi-heures.
     *
     * Sur la base des horaires d'une scéance au format «HH:mm - HH:mm»
     * calcule la durée en nombre de demi-heures.
     * Servira à calculer combien de lignes d'affichage occupera une
     * scéance.
     *
     * @param  String $heures
     * @return Int
     */
    function dureeSeance($heures)
    {
        $aHeures = explode("-", $heures);
        $aDebut = explode(":", $aHeures[0]);
        $aFin   = explode(":", $aHeures[1]);
        $iDebut = ($aDebut[0] * 60) + $aDebut[1];
        $iFin   = ($aFin[0] * 60) + $aFin[1];
        $duree = ($iFin - $iDebut) / 15;
        return $duree;
    }
    /**
     * Construction des liens vers les fiches détaillées des conférences.
     *
     * @param  String $infoSeance
     * @return String
     */
    function lienSeance($infoSeance,$for_bo)
    {
        $masque = "#^([0-9]+) ?: ?(.*)#";
        //$masque = "#^([0-9]+) ?| ?(.*) ?| ?(.*)#";
        $lien = preg_replace($masque, '<p><a href="'.($for_bo?'':'./sessions.php').'#$1"  name="ag_sess_$1">$2</a></p>', $infoSeance);
        return $lien;
    }

    function genAgenda($annee, $for_bo =false, $only_data=false, $forum_id = null)
    {
    $aAgenda = $this->obtenirAgenda($annee, $forum_id);
    //var_dump($aAgenda);
if(isset($aAgenda) && count($aAgenda) > 0)
{
    $nbConf = count($aAgenda);
    $nomSalles = array();
    $j = 0;
    $d = null;
    $aProgramme = array();
    foreach ($aAgenda as $index => $session)
    {
        if(!isset($nomSalles[$session['id_salle']]))
        {
            $nomSalles[$session['id_salle']] = $session['nom'];
        }
        $dj = $session['jour'];
        if($dj != $d)
        {
            $j++;
            $d = $dj;
            $aProgramme[$dj] = array();
        }
        if(!isset($aProgramme[$dj][$session['debut'] ."-". $session['fin']]))
        {
            $aProgramme[$dj][$session['debut'] ."-". $session['fin']] = array();
        }
        if(!isset($aProgramme[$dj][$session['debut'] ."-". $session['fin']][$session['nom']]))
        {
            $aProgramme[$dj][$session['debut'] ."-". $session['fin']][$session['nom']] = array();
        }
        $aProgrammeData[$dj][$session['debut'] ."-". $session['fin']][] = $session;
        $aProgramme[$dj][$session['debut'] ."-". $session['fin']][$session['nom']][] = $session['session_id'] ." : ". $session['titre'].(' <span class="conferencier">'.$session['conf1'].($session['conf2']?(' / '.$session['conf2']):'').'</span>');
        //$aProgramme[$dj][$session['debut'] ."-". $session['fin']][$session['nom']][] = array('id'=>$session['session_id'], 'titre'=> $session['titre'],'conf1'=> $session['titre'],'titre'=> $session['titre']);
    }
    //var_dump($aProgrammeData['12-11-2009']);die;
    if ($only_data)
    {
    	return $aProgrammeData;
    }
    $nbSalles = count($nomSalles);
    $tdWith = round(84/$nbSalles);

    //
    $sTable = '';
    $j = 1;
    $aRowSpan = array();

    /* On boucle sur chaque journée du programme. */
    foreach($aProgramme as $journee => $aInfos)
    {
      $journee_aff = date('d/m/Y',strtotime($journee)) ;
      $sTable .= <<<CODE_HTML
            <table summary="Agenda du forum">
              <caption>Jour {$j} : {$journee_aff}</caption>
              <thead>
                <tr>
                  <th class="horaire">&nbsp;</th>

CODE_HTML;
        $s = 1;
        foreach($nomSalles as $idSalle => $nomSalle)
        {
            $sTable .= <<<CODE_HTML
                  <th class="activite">{$nomSalle}</th>

CODE_HTML;
            $aRowSpan[$idSalle] = 0;
            $s++;
        }
        $sTable .= <<<CODE_HTML
                </tr>
              </thead>
              <tbody>

CODE_HTML;
        /* On boucle maintenant sur chaque demi-heure de l'agenda (de 09h00 à 18h00 */
        for($h = 9; $h < 18; $h++)
        {
          for($i = 0; $i < 4; $i++)
            {
            $bKeynote = false;
                $m = sprintf('%02d', 15 * $i);
                $m_next = sprintf('%02d', (15 * ($i + 1)) % 60);
                $style = ($i % 2 == 0) ? 'lp' : 'li';
                $sHeure = ($h < 10) ? '0'. $h : $h;
                $h_next = ($i < 3) ? $h : $h+1;
                $sHeure_next = ($h_next < 10) ? '0'. $h_next : $h_next;
                /* Création de la ligne avec la cellule indiquant l'heure */
                $sTable .= <<<CODE_HTML
                <tr class="{$style}">
                  <td class="col_heure" nowrap="nowrap">{$sHeure}h{$m} - {$sHeure_next}h{$m_next} </td>

CODE_HTML;

                /* On cherche les scéances commençant à cette heure pour chaque salle. */
                foreach($nomSalles as $idSalle => $nomSalle)
                {
                    /* On vérifie qu'on est pas déjà sur une scéance commencée à un tour précédent. */
                    if($aRowSpan[$idSalle] <= 1):
                        $bSeance = false;
                        $rs = null;
                        /* Calcul du nombre de lignes occupées par la scéance s'il y en a une. */
                        for($c = 0; $c < $nbConf; $c++):
                            //var_dump($aAgenda[$c]);
                            if(
                                $aAgenda[$c]['debut'] == $sHeure .":". $m &&
                                $aAgenda[$c]['id_salle'] == $idSalle &&
                                $aAgenda[$c]['jour'] == $journee
                            ):
                                /* Si on toruve une scéance, on ne mettra pas de cellule vide. */
                                $bSeance = true;

                                $bKeynote = $aAgenda[$c]['keynote'];
                                $colspan = $bKeynote?' colspan="'.$nbSalles.'" class="keynote" ':'';
                                $heures = $aAgenda[$c]['debut'] ."-". $aAgenda[$c]['fin'];
                                $nl = $this->dureeSeance($heures);
                                $aRowSpan[$idSalle] = $nl;
                                $rs = ($nl > 1) ? ' rowspan="'. $nl .'"' : null;
                                $nbSeances = (isset($aInfos[$heures][$nomSalle])) ? count($aInfos[$heures][$nomSalle]) : 0;
                                if($nbSeances > 0):
                                    $conflit = $nbSeances > 1 ? ' style="color: inherit; background-color: #f99"' : null;
                                    $sTable .= <<<CODE_HTML
                  <td{$rs}{$conflit} width="{$tdWith}%" {$colspan} >

CODE_HTML;
                                    for($sc = 0; $sc < $nbSeances; $sc++):

                                        $lien = $this->lienSeance($aInfos[$heures][$nomSalle][$sc], $for_bo);
                                        //$lien = '<p><a href="'.($for_bo?'':'./sessions.php').'#$1"  name="ag_sess_$1">$2</a></p>';
                                        $sTable .=  $lien;
                                    endfor;
                                $sTable .= "</td>";
                                endif;
                                break;
                            endif;
                        endfor;
                        if (in_array($sHeure.'_'.$m.'_'.$journee,array('17_00_12-11-2009','10_30_12-11-2009')))
                        {
                        	$bKeynote = true;
                        }
                        if(false === $bSeance && !$bKeynote):
                                $sTable .= "<td>&nbsp;</td>";
                        endif;
                    else:
                        $aRowSpan[$idSalle]--;
                    endif;
                }
                $sTable .= " </tr>";
            }
        }
        $sTable .= <<<CODE_HTML
              </tbody>
            </table><br class="page_break">

CODE_HTML;
        $j++;
    }
}
else
{
    // Aucune donnée dans la base. Affichage alternatif.
    $sTable = <<<CODE_HTML
            <h3>Aucune entrée disponible.</h3>

CODE_HTML;
}
return  $sTable;
    }

    function obtenirCsvJoindIn($id_forum)
    {
        $id_forum = $this->_bdd->echapper($id_forum);

        // Récupération des données
        $requete = "
        SELECT afup_sessions.titre, afup_sessions.abstract, afup_sessions.genre, afup_sessions.journee,
        	   DATE_FORMAT(FROM_UNIXTIME(afup_forum_planning.debut), '%Y-%m-%d') AS date,
			   DATE_FORMAT(FROM_UNIXTIME(afup_forum_planning.debut), '%H:%i') AS heure,
			   afup_forum_planning.keynote,

        	(SELECT CONCAT(afup_conferenciers1.prenom, ' ', afup_conferenciers1.nom)
        	 FROM afup_conferenciers_sessions AS afup_conferenciers_sessions
                INNER JOIN afup_conferenciers AS afup_conferenciers1 ON afup_conferenciers1.conferencier_id = afup_conferenciers_sessions.conferencier_id
        	 WHERE afup_conferenciers_sessions.session_id = afup_sessions.session_id
                LIMIT 0,1) AS conferencier1,

        	(SELECT CONCAT(afup_conferenciers2.prenom, ' ', afup_conferenciers2.nom)
        	 FROM afup_conferenciers_sessions AS afup_conferenciers_sessions
                INNER JOIN afup_conferenciers AS afup_conferenciers2 ON afup_conferenciers2.conferencier_id = afup_conferenciers_sessions.conferencier_id
        	 WHERE afup_conferenciers_sessions.session_id = afup_sessions.session_id
                LIMIT 1,1) AS conferencier2

        FROM afup_sessions
        INNER JOIN afup_forum_planning ON afup_forum_planning.id_session = afup_sessions.session_id
        WHERE afup_sessions.id_forum = $id_forum AND afup_sessions.plannifie = 1;";
        $donnees = $this->_bdd->obtenirTous($requete);

        // Génération des données CSV
        $csv = "Title,Description,Speaker,Date,Time,Type\n";
        foreach ($donnees as $conference) {

            // Gestion de la description
            $description = html_entity_decode($conference['abstract'], null, 'UTF-8');
            $description = strip_tags($description);
            $description = str_replace('"', '\"', $description);

            // Gestion des conférenciers
            $conferenciers = array();
            for ($i = 1; $i <= 2; $i++) {
                if (!empty($conference['conferencier' . $i]) &&
                	'En cours de validation' != trim($conference['conferencier' . $i])) {
                    $conferenciers[] = $conference['conferencier' . $i];
                }
            }
            if (empty($conferenciers)) {
                $conferenciers[] = '-';
            }
            $conferenciers = implode(',', $conferenciers);

            // Gestion du type de conférence
            if (1 == $conference['keynote']) {
                $type = 'Keynote';
            } elseif (2 == $conference['genre']) {
                $type = 'Workshop';
            } else {
                $type = 'Talk';
            }

            $csv .= sprintf(
            	"\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $conference['titre'],
                $description,
                $conferenciers,
                $conference['date'],
                $conference['heure'],
                $type
            );
        }

        return $csv;
    }

    function obtenirXmlPourAppliIphone($id_forum)
    {
        $id_forum = $this->_bdd->echapper($id_forum);

        // Récupération des données
        $requete = "SELECT
                        afup_sessions.session_id, afup_sessions.titre, afup_sessions.abstract, afup_sessions.genre,
                        afup_sessions.journee, afup_forum_planning.debut, afup_forum_planning.fin, afup_forum_planning.keynote
                    FROM
                        afup_sessions
                    INNER JOIN
                        afup_forum_planning ON afup_forum_planning.id_session = afup_sessions.session_id
                    WHERE
                        afup_sessions.id_forum = $id_forum
                        AND afup_sessions.plannifie = 1
                    ORDER BY
                        afup_forum_planning.debut;";
        $donnees = $this->_bdd->obtenirTous($requete);

        $requeteSpeaker = "SELECT
                               afup_conferenciers.*
                           FROM
                               afup_conferenciers_sessions AS afup_conferenciers_sessions
                           INNER JOIN
                               afup_conferenciers ON afup_conferenciers.conferencier_id = afup_conferenciers_sessions.conferencier_id
                           WHERE
                               afup_conferenciers_sessions.session_id = ";
        // Génération des données XML
        $xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<data></data>";
        $xml = new SimpleXMLElement($xmlstr);
        $sessions = $xml->addChild('sessions');
        $bios = $xml->addChild('bios');
        $dejaConferencier = array();
        foreach ($donnees as $d) {
            $session = $sessions->addChild('session');
            $session->addAttribute('title', $d['titre']);
            $session->addAttribute('starts', date(DATE_ISO8601, $d['debut']));
            $session->addAttribute('ends', date(DATE_ISO8601, $d['fin']));
            $listeSpeaker = $this->_bdd->obtenirTous($requeteSpeaker . $d['session_id']);
            foreach ($listeSpeaker as $s) {
                $speaker = $session->addChild('speaker');
                $speaker->addAttribute('id', $s['conferencier_id']);
                $speaker->addAttribute('org', $s['societe']);
                if (!in_array($s['conferencier_id'], $dejaConferencier)) {
                    $bio = $bios->addChild('bio', str_replace('"', '\"', htmlspecialchars($s['biographie'])));
                    $bio->addAttribute('id', $s['conferencier_id']);
                    $bio->addAttribute('name', $s['prenom'] . ' ' . $s['nom']);
                    $dejaConferencier[] = $s['conferencier_id'];
                }
            }
            $sujet = $session->addChild('abstract', str_replace('"', '\"', htmlspecialchars($d['abstract'])));
        }
        return $xml->asXml();
    }

    function ajouter($titre, $nb_places, $date_debut, $date_fin, $date_fin_appel_projet,
                      $date_fin_appel_conferencier, $date_fin_prevente, $date_fin_vente, $chemin_template)
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_forum (id, titre, nb_places, date_debut, date_fin, annee, date_fin_appel_projet,';
        $requete .= '  date_fin_appel_conferencier, date_fin_prevente, date_fin_vente, path) ';
        $requete .= 'VALUES (null,';
        $requete .= $this->_bdd->echapper($titre)                                                 . ',';
        $requete .= (int) $nb_places                                                              . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_debut)                        . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin)                          . ',';
        $requete .= (int) $date_debut['Y']                                                        . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_appel_projet, true)       . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_appel_conferencier, true) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_prevente, true)           . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_vente, true)              . ',';
        $requete .= $this->_bdd->echapper($chemin_template, true)                                 . ')';

        return $this->_bdd->executer($requete);
    }

    function modifier($id, $titre, $nb_places, $date_debut, $date_fin, $date_fin_appel_projet,
                       $date_fin_appel_conferencier, $date_fin_prevente, $date_fin_vente, $chemin_template)
    {
        $requete  = 'UPDATE ';
        $requete .= '  afup_forum ';
        $requete .= 'SET';
        $requete .= '  titre='                 . $this->_bdd->echapper($titre)                                           . ',';
        $requete .= '  nb_places='             . (int) $nb_places                                                        . ',';
        $requete .= '  date_debut='            . $this->_bdd->echapperSqlDateFromQuickForm($date_debut)                  . ',';
        $requete .= '  date_fin='              . $this->_bdd->echapperSqlDateFromQuickForm($date_fin)                    . ',';
        $requete .= '  annee='                 . (int) $date_debut['Y']                                                  . ',';
        $requete .= '  date_fin_appel_projet=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_appel_projet, true) . ',';
        $requete .= '  date_fin_appel_conferencier=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_appel_conferencier, true) . ',';
        $requete .= '  date_fin_prevente='     . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_prevente, true)     . ',';
        $requete .= '  date_fin_vente='        . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_vente, true)        . ',';
        $requete .= '  path='                  . $this->_bdd->echapper($chemin_template, true)                           . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;

        return $this->_bdd->executer($requete);
    }

    function supprimer($id_forum) {
        $id_forum = $this->_bdd->echapper($id_forum);

        $requete  = 'DELETE FROM afup_forum WHERE id = '.$id_forum;

        return $this->_bdd->executer($requete);
    }
}
