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
        $requete .= '  ' . $champs . ', year(date_debut) as forum_annee ';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirNombrePlaces($id=NULL) {
      if (empty($id)) {
        $id = $this->obtenirDernier();
      }
      $enregistrement = $this->obtenir($id, 'nb_places');

      return  $enregistrement['nb_places'];
    }

    function obtenirDernier()
    {
        $requete  = 'SELECT MAX(id)';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
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

    /**
     * Récupérer l'agenda du forum.
     *
     * Pour une année donnée pass�e en paramètre, retourne
     * les informations nécessaires à la construction du tableau
     * de l'agenda du forum AFUP correspondant.
     *
     * @param Int $annee (Optionnel, retournera tout si aucunne année indiquée)
     */
    function obtenirAgenda($annee = null)
    {
        $sWhere = array();
        if(isset($annee))
        {
            $tdebut = mktime(0,0,0,1,1,$annee);
            $tfin   = mktime(0,0,0,1,1,($annee + 1));
            $aWhere[] = "p.debut >= ". $tdebut;
            $aWhere[] = "p.fin < ". $tfin;
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
        require_once AFUP_CHEMIN_RACINE . 'classes/phpmailer/class.phpmailer.php';
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
        $duree = ($iFin - $iDebut) / 30;
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

    function genAgenda($annee, $for_bo =false, $only_data=false)
    {
    $aAgenda = $this->obtenirAgenda($annee);
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
          for($i = 0; $i < 2; $i++)
            {
            $bKeynote = false;
                $m = ($i % 2 == 0) ? '00' : '30';
                $m_next = ($i % 2 == 0) ? '30' : '00';
                $style = ($i % 2 == 0) ? 'lp' : 'li';
                $sHeure = ($h < 10) ? '0'. $h : $h;
                $h_next =($i % 2 == 0) ? $h : $h+1;
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
}
?>