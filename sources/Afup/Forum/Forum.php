<?php

declare(strict_types=1);

namespace Afup\Site\Forum;

use Afup\Site\Utils\Base_De_Donnees;

class Forum
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
        $requete .= '  ' . $champs . ', annee as forum_annee ';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public function supprimable(string $id): bool
    {
        $requete = 'SELECT';
        $requete .= '  f.id, count(session_id) as sessions,count(i.id) as inscriptions ';
        $requete .= 'FROM';
        $requete .= '  afup_forum f ';
        $requete .= 'LEFT JOIN afup_sessions s ON (f.id = s.id_forum) ';
        $requete .= 'LEFT JOIN afup_inscription_forum i ON (f.id = i.id_forum) ';
        $requete .= 'WHERE f.id=' . $id;

        $forum = $this->_bdd->obtenirEnregistrement($requete);

        return $forum['sessions'] == 0 && $forum['inscriptions'] == 0;
    }

    public function obtenirNombrePlaces($id = null)
    {
        if (empty($id)) {
            $id = $this->obtenirDernier();
        }
        $enregistrement = $this->obtenir($id, 'nb_places');

        return $enregistrement['nb_places'];
    }

    public function obtenirDebut($id_forum)
    {
        $requete = 'SELECT UNIX_TIMESTAMP(date_debut)';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
        $requete .= 'WHERE';
        $requete .= '  id =  ' . (int) $id_forum;
        return $this->_bdd->obtenirUn($requete);
    }

    public function obtenirForumPrecedent($id_forum)
    {
        $requete = 'SELECT MAX(id)';
        $requete .= 'FROM';
        $requete .= '  afup_forum ';
        $requete .= 'WHERE';
        $requete .= '  id <  ' . (int) $id_forum . ' AND titre like "%Forum%"';
        return $this->_bdd->obtenirUn($requete);
    }

    public function obtenirDernier()
    {
        $requete = 'SELECT id ';
        $requete .= 'FROM afup_forum ';
        $requete .= 'ORDER BY date_debut desc';
        return $this->_bdd->obtenirUn($requete);
    }

    /**
     * Renvoit la liste des inscriptions à facturer ou facturé au forum
     *
     * @param  string $champs Champs à renvoyer
     * @param  string $ordre Tri des enregistrements
     * @param  bool $associatif Renvoyer un tableau associatif ?
     * @return array
     */
    public function obtenirListe($id_forum = null,
                          string $champs = '*',
                          string $ordre = 'titre',
                          $associatif = false,
                          $filtre = false)
    {
        $requete = 'SELECT';
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

    public function obtenirListActive()
    {
        return $this->_bdd->obtenirTous('SELECT * FROM afup_forum WHERE archived_at IS NULL ORDER BY titre');
    }

    public function afficherDeroulementMobile($sessions): string
    {
        $deroulement = "<div class=\"deroulements\">";
        $jour = 0;
        $heure = 0;
        foreach ($sessions as $session) {
            if ($jour != mktime(0, 0, 0, (int) date("m", $session['debut']), (int) date("d", $session['debut']), (int) date("Y", $session['debut']))) {
                $jour = mktime(0, 0, 0, (int) date("m", $session['debut']), (int) date("d", $session['debut']), (int) date("Y", $session['debut']));
                $deroulement .= "<h2 class=\"jour\">" . ($jour > 10000 ? date("d/m/Y", $jour) : 'Jour à définir') . "</h2>";
            }
            if ($heure != $session['debut']) {
                $heure = $session['debut'];
                $deroulement .= "<h3 class=\"horaire\">" . date("H\hi", $heure) . "</h3>";
            }

            $classes = ["deroulement"];
            $classes[] = $session['journee'];
            if ($session['keynote'] == 1) {
                $classes[] = "keynote";
            }

            $conferenciers = $session['conf1'];
            if (!empty($session['conf2'])) {
                $conferenciers .= "<br />" . $session['conf2'];
            }

            $deroulement .= "<div class=\"" . implode(" ", $classes) . "\">";
            $deroulement .= "    <div class=\"session\"><a href=\"sessions.php#" . $session['session_id'] . "\">" . $session['titre'] . "</a></div>";
            $deroulement .= "    <div class=\"conferenciers\">" . $conferenciers . "</div>";
            $deroulement .= "    <div class=\"salle\">" . $session['nom_salle'] . "</div>";
            $deroulement .= "</div>";
        }

        return $deroulement . "</div>";
    }

    public function afficherDeroulement($sessions): string
    {
        $deroulement = "<div class=\"deroulements\">";
        $jour = 0;
        $heure = 0;
        foreach ($sessions as $session) {
            if ($jour != mktime(0, 0, 0, (int) date("m", $session['debut']), (int) date("d", $session['debut']), (int) date("Y", $session['debut']))) {
                $jour = mktime(0, 0, 0, (int) date("m", $session['debut']), (int) date("d", $session['debut']), (int) date("Y", $session['debut']));
                $deroulement .= "<h2 class=\"jour\">" . ($jour > 10000 ? date("d/m/Y", $jour) : 'Jour à définir') . "</h2>";
            }
            if ($heure != $session['debut']) {
                $heure = $session['debut'];
                $deroulement .= "<h3 class=\"horaire\">" . date("H\hi", $heure) . "</h3>";
            }

            $classes = ["deroulement"];
            $classes[] = $session['journee'];
            if ($session['keynote'] == 1) {
                $classes[] = "keynote";
            }

            $conferenciers = $session['conf1'];
            if (!empty($session['conf2'])) {
                $conferenciers .= "<br />" . $session['conf2'];
            }

            $deroulement .= "<div class=\"" . implode(" ", $classes) . "\">";
            $deroulement .= "    <div class=\"session\"><a href=\"sessions.php#" . $session['session_id'] . "\">" . $session['titre'] . "</a></div>";
            $deroulement .= "    <div class=\"conferenciers\">" . $conferenciers . "</div>";
            $deroulement .= "</div>";
        }

        return $deroulement . "</div>";
    }

    public function afficherAgenda($sessions): string
    {
        $slots = [];
        $salles = [];
        $debuts = [];
        foreach ($sessions as $session) {
            $jour = mktime(0, 0, 0, (int) date("m", $session['debut']), (int) date("d", $session['debut']), (int) date("Y", $session['debut']));
            $slots[$jour][$session['nom_salle']][$session['debut']] = $session;
            $debuts[$jour] = isset($debuts[$jour]) ? min($session['debut'], $debuts[$jour]) : $session['debut'];
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
            $agenda .= "<h2 style=\"position: absolute; width: 100%; top: " . round($passage_jour * 1600) . "px;\">" . date("d/m/Y", $jour) . "</h2>";
            foreach ($slots_avec_salle as $slots_avec_horaire) {
                foreach ($slots_avec_horaire as $session) {
                    $classes = ["slot"];
                    $classes[] = $session['journee'];

                    $conferenciers = $session['conf1'];
                    if (!empty($session['conf2'])) {
                        $conferenciers .= "<br />" . $session['conf2'];
                    }

                    $styles = ["position: absolute;"];
                    if ($session['keynote'] == 1) {
                        $classes[] = "keynote";
                        $styles[] = "width: 100%;";
                        $styles[] = "left: 0%;";
                    } else {
                        $styles[] = "width: " . round(100 / $nb_salles) . "%;";
                        $styles[] = "left: " . ($salles[$session['id_salle']] * round(100 / $nb_salles)) . "%;";
                    }
                    $styles[] = "height: " . round(($session['fin'] - $session['debut']) / 19) . "px;";
                    $styles[] = "top: " . round(40 + $passage_jour * 1600 + ($session['debut'] - $debuts[$jour]) / 19) . "px;";

                    $agenda .= "<div class=\"" . implode(" ", $classes) . "\" style=\"" . implode(" ", $styles) . "\">";
                    $agenda .= "    <div class=\"session\"><a href=\"sessions.php#" . $session['session_id'] . "\">" . $session['titre'] . "</a></div>";
                    $agenda .= "    <div class=\"conferenciers\">" . $conferenciers . "</div>";
                    $agenda .= "    <div class=\"horaire\">" . date("H\hi", $session['debut']) . " - " . date("H\hi", $session['fin']) . "</div>";
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
    public function obtenirAgenda($annee = null, $forum_id = null)
    {
        $aWhere = [];
        if (isset($annee)) {
            $tdebut = mktime(0, 0, 0, 1, 1, (int) $annee);
            $tfin = mktime(0, 0, 0, 1, 1, (int) ($annee + 1));
            $aWhere[] = "p.debut >= " . $tdebut;
            $aWhere[] = "p.fin < " . $tfin;
            $aWhere[] = "s.plannifie = 1";
        }

        if (null !== $forum_id) {
            $aWhere[] = "l.id_forum = " . $forum_id;
        }

        $sWhere = "WHERE " . implode(" AND ", $aWhere);
        $requete = "SELECT " .
            " ( SELECT CONCAT(c.nom,' ', c.prenom , ' - ', c.societe )  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = s.session_id order by c.conferencier_id asc limit 1) as conf1 ,
                      ( SELECT CONCAT(c.nom,' ', c.prenom, ' - ', c.societe)  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = s.session_id order by c.conferencier_id asc limit 1,1) as conf2 , " .

            "    s.session_id, s.titre, s.journee, " .
            "    FROM_UNIXTIME(p.debut, '%d-%m-%Y') AS 'jour', " .
            "    FROM_UNIXTIME(p.debut, '%H:%i') AS 'debut', " .
            "    FROM_UNIXTIME(p.fin, '%H:%i') AS 'fin', " .
            "    p.id_salle, " .
            "    p.keynote, " .
            "    l.nom " .
            "FROM   afup_sessions       s " .
            "  JOIN afup_forum_planning p ON s.session_id = p.id_session " .
            "  JOIN afup_forum_salle    l ON p.id_salle   = l.id " .
            $sWhere . " " .
            "ORDER BY p.debut ASC, p.id_salle ASC";
        return $this->_bdd->obtenirTous($requete);
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
    public function dureeSeance($heures)
    {
        $aHeures = explode("-", $heures);
        $aDebut = explode(":", $aHeures[0]);
        $aFin = explode(":", $aHeures[1]);
        $iDebut = ((int) $aDebut[0] * 60) + (int) $aDebut[1];
        $iFin = ((int) $aFin[0] * 60) + (int) $aFin[1];
        return ($iFin - $iDebut) / 5;
    }

    /**
     * Construction des liens vers les fiches détaillées des conférences.
     *
     * @param  String $infoSeance
     * @param  Boolean $for_bo
     * @param  string $linkFormat if $for_bo = false, this format will be used (if not null) to construct the link.
     *                  i.e : "/sessions.php#%1" . %1 is the session id
     * @return String
     */
    public function lienSeance($infoSeance, $for_bo, $linkFormat): ?string
    {
        $masque = "#^(\\d+) ?: ?(.*)#";
        //$masque = "#^([0-9]+) ?| ?(.*) ?| ?(.*)#";


        $lien = '#$1';
        if ($for_bo === false) {
            $lien = $linkFormat !== null ? sprintf($linkFormat, '$1') : './sessions.php#$1';
        }
        return preg_replace($masque, '<p><a href="' . $lien . '"  name="ag_sess_$1">$2</a></p>', $infoSeance);
    }

    public function genAgenda($annee, $for_bo = false, $only_data = false, $forum_id = null, $linkFormat = null)
    {
        $aProgrammeData = [];
        $aAgenda = $this->obtenirAgenda($annee, $forum_id);
        if (isset($aAgenda) && count($aAgenda) > 0) {
            $nbConf = count($aAgenda);
            $nomSalles = [];
            $j = 0;
            $d = null;
            $aProgramme = [];
            foreach ($aAgenda as $session) {
                if (!isset($nomSalles[$session['id_salle']])) {
                    $nomSalles[$session['id_salle']] = $session['nom'];
                }
                $dj = $session['jour'];
                if ($dj != $d) {
                    $j++;
                    $d = $dj;
                    $aProgramme[$dj] = [];
                }
                if (!isset($aProgramme[$dj][$session['debut'] . "-" . $session['fin']])) {
                    $aProgramme[$dj][$session['debut'] . "-" . $session['fin']] = [];
                }
                if (!isset($aProgramme[$dj][$session['debut'] . "-" . $session['fin']][$session['nom']])) {
                    $aProgramme[$dj][$session['debut'] . "-" . $session['fin']][$session['nom']] = [];
                }
                $aProgrammeData[$dj][$session['debut'] . "-" . $session['fin']][] = $session;
                $aProgramme[$dj][$session['debut'] . "-" . $session['fin']][$session['nom']][] = $session['session_id'] . " : " . $session['titre'] . (' <span class="conferencier">' . $session['conf1'] . ($session['conf2'] ? (' / ' . $session['conf2']) : '') . '</span>');
                //$aProgramme[$dj][$session['debut'] ."-". $session['fin']][$session['nom']][] = array('id'=>$session['session_id'], 'titre'=> $session['titre'],'conf1'=> $session['titre'],'titre'=> $session['titre']);
            }
            //var_dump($aProgrammeData['12-11-2009']);die;
            if ($only_data) {
                return $aProgrammeData;
            }
            $nbSalles = count($nomSalles);
            $tdWith = round(84 / $nbSalles);

            //
            $sTable = '';
            $j = 1;
            $aRowSpan = [];

            /* On boucle sur chaque journée du programme. */
            foreach ($aProgramme as $journee => $aInfos) {
                $journee_aff = date('d/m/Y', strtotime($journee));
                $sTable .= <<<CODE_HTML
<div class="ui segment">
<h2 class="ui header">Jour {$j} : {$journee_aff}</h2>
        <div class="ui clearing divider"></div>
            <table summary="Agenda du forum" class="ui table striped compact celled">
              <thead>
                <tr>
                  <th class="horaire">&nbsp;</th>

CODE_HTML;
                $s = 1;
                $confNumber = 0;
                foreach ($nomSalles as $idSalle => $nomSalle) {
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
                /* On boucle maintenant sur chaque demi-heure de l'agenda (de 08h00 à 18h00 */
                for ($h = 8; $h < 19; $h++) {
                    for ($i = 0; $i < 12; $i++) {
                        $bKeynote = false;
                        $m = sprintf('%02d', 5 * $i);
                        $m_next = sprintf('%02d', (5 * ($i + 1)) % 60);
                        $style = ($i % 2 == 0) ? 'lp' : 'li';
                        $sHeure = ($h < 10) ? '0' . $h : $h;
                        $h_next = ($i < 11) ? $h : $h + 1;
                        $sHeure_next = ($h_next < 10) ? '0' . $h_next : $h_next;
                        /* Création de la ligne avec la cellule indiquant l'heure */
                        $sTable .= <<<CODE_HTML
                <tr class="{$style}">
                  <td class="col_heure" nowrap="nowrap"><span class="heure_debut">{$sHeure}h{$m}</span><span class="heure_fin"> - {$sHeure_next}h{$m_next}</span> </td>

CODE_HTML;

                        /* On cherche les scéances commençant à cette heure pour chaque salle. */
                        foreach ($nomSalles as $idSalle => $nomSalle) {
                            /* On vérifie qu'on est pas déjà sur une scéance commencée à un tour précédent. */
                            if ($aRowSpan[$idSalle] <= 1):
                                $bSeance = false;
                            $rs = null;
                            /* Calcul du nombre de lignes occupées par la scéance s'il y en a une. */
                            for ($c = 0; $c < $nbConf; $c++):
                                    //var_dump($aAgenda[$c]);
                                    if (
                                        $aAgenda[$c]['debut'] == $sHeure . ":" . $m &&
                                        $aAgenda[$c]['id_salle'] == $idSalle &&
                                        $aAgenda[$c]['jour'] == $journee
                                    ):
                                        /* Si on toruve une scéance, on ne mettra pas de cellule vide. */
                                        $bSeance = true;

                            $bKeynote = $aAgenda[$c]['keynote'];
                            $colspan = $bKeynote ? ' colspan="' . $nbSalles . '" class="keynote" ' : '';
                            $heures = $aAgenda[$c]['debut'] . "-" . $aAgenda[$c]['fin'];
                            $nl = $this->dureeSeance($heures);
                            $aRowSpan[$idSalle] = $nl;

                            $class = 'conf conf_' . ($confNumber % 2 === 0 ? 'odd' : 'even');

                            $rs = ($nl > 1) ? ' rowspan="' . $nl . '"' : null;
                            $nbSeances = (isset($aInfos[$heures][$nomSalle])) ? count($aInfos[$heures][$nomSalle]) : 0;
                            if ($nbSeances > 0):
                                            $conflit = $nbSeances > 1 ? ' style="color: inherit; background-color: #f99"' : null;
                            $sTable .= <<<CODE_HTML
                  <td{$rs}{$conflit} width="{$tdWith}%" {$colspan} class="{$class}" >

CODE_HTML;
                            for ($sc = 0; $sc < $nbSeances; $sc++):

                                                $lien = $this->lienSeance($aInfos[$heures][$nomSalle][$sc], $for_bo, $linkFormat);
                            //$lien = '<p><a href="'.($for_bo?'':'./sessions.php').'#$1"  name="ag_sess_$1">$2</a></p>';
                            $sTable .= $lien;
                            endfor;
                            $sTable .= "</td>";
                            $confNumber++;
                            endif;
                            break;
                            endif;
                            endfor;
                            if (in_array($sHeure . '_' . $m . '_' . $journee, ['17_00_12-11-2009', '10_30_12-11-2009'])) {
                                $bKeynote = true;
                            }
                            if (false === $bSeance && !$bKeynote):
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
            </table></div><br class="page_break">

CODE_HTML;
                $j++;
            }
        } else {
            // Aucune donnée dans la base. Affichage alternatif.
            $sTable = <<<CODE_HTML
            <h3>Aucune entrée disponible.</h3>

CODE_HTML;
        }
        return $sTable;
    }

    public function obtenirCsvJoindIn($id_forum): string
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
            $description = html_entity_decode((string) $conference['abstract'], ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
            $description = strip_tags($description);
            $description = str_replace('"', '\"', $description);

            // Gestion des conférenciers
            $conferenciers = [];
            for ($i = 1; $i <= 2; $i++) {
                if (!empty($conference['conferencier' . $i]) &&
                    'En cours de validation' !== trim((string) $conference['conferencier' . $i])
                ) {
                    $conferenciers[] = $conference['conferencier' . $i];
                }
            }
            if ($conferenciers === []) {
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

    public function ajouter(
        $titre,
        $nb_places,
        array $date_debut,
        $date_fin,
        $date_fin_appel_projet,
        $date_fin_appel_conferencier,
        $date_fin_vote,
        $date_fin_prevente,
        $date_fin_vente,
        $date_fin_vente_token_sponsor,
        $date_fin_saisie_repas_speakers,
        $date_fin_saisie_nuites_hotel,
        $date_annonce_planning,
        $chemin_template,
        array $text,
        $logoUrl,
        $placeName,
        $placeAddress,
        $voteEnabled = true,
        $speakersDinerEnabled = true,
        $accomodationEnabled = true,
        $waitingListUrl = null,
        $hasPricesDefinedWithVat = false,
        $transportInformationEnabled = false,
    ) {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_forum (id, titre, nb_places, date_debut, date_fin, annee, date_fin_appel_projet,';
        $requete .= '  date_fin_appel_conferencier, date_fin_vote, date_fin_prevente, date_fin_vente, date_fin_vente_token_sponsor, date_fin_saisie_repas_speakers, date_fin_saisie_nuites_hotel, date_annonce_planning, path, `text`,
        `logo_url`, `place_name`, `has_prices_defined_with_vat`, `vote_enabled`, `speakers_diner_enabled`, `accomodation_enabled`, `waiting_list_url`, `place_address`, `transport_information_enabled`) ';
        $requete .= 'VALUES (null,';
        $requete .= $this->_bdd->echapper($titre) . ',';
        $requete .= (int) $nb_places . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_debut) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin) . ',';
        $requete .= (int) $date_debut['Y'] . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_appel_projet, true) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_appel_conferencier, true) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_vote, false) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_prevente, true) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_vente, true) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_vente_token_sponsor, true) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_saisie_repas_speakers, true) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_fin_saisie_nuites_hotel, true) . ',';
        $requete .= $this->_bdd->echapperSqlDateFromQuickForm($date_annonce_planning, true) . ',';
        $requete .= $this->_bdd->echapper($chemin_template) . ',';
        $requete .= $this->_bdd->echapper(json_encode($text)) . ', ';
        $requete .= $this->_bdd->echapper($logoUrl) . ',';
        $requete .= $this->_bdd->echapper($placeName) . ',';
        $requete .= $this->_bdd->echapper($hasPricesDefinedWithVat ? 1 : 0) . ',';
        $requete .= $this->_bdd->echapper($voteEnabled ? 1 : 0) . ',';
        $requete .= $this->_bdd->echapper($speakersDinerEnabled ? 1 : 0) . ',';
        $requete .= $this->_bdd->echapper($accomodationEnabled ? 1 : 0) . ',';
        $requete .= $this->_bdd->echapper($waitingListUrl) . ',';
        $requete .= $this->_bdd->echapper($placeAddress) . ',';
        $requete .= $this->_bdd->echapper($transportInformationEnabled ? 1 : 0);

        $requete .= ')';

        return $this->_bdd->executer($requete);
    }

    public function modifier(
        string $id,
        $titre,
        $nb_places,
        array $date_debut,
        $date_fin,
        $date_fin_appel_projet,
        $date_fin_appel_conferencier,
        $date_fin_vote,
        $date_fin_prevente,
        $date_fin_vente,
        $date_fin_vente_token_sponsor,
        $date_fin_saisie_repas_speakers,
        $date_fin_saisie_nuites_hotel,
        $date_annonce_planning,
        $chemin_template,
        array $text,
        $logoUrl = null,
        $placeName = null,
        $placeAddress = null,
        $voteEnabled = true,
        $speakersDinerEnabled = true,
        $accomodationEnabled = true,
        $waitingListUrl = null,
        $hasPricesDefinedWithVat = false,
        $transportInformationEnabled = false,
    ) {
        $requete = 'UPDATE ';
        $requete .= '  afup_forum ';
        $requete .= 'SET';
        $requete .= '  titre=' . $this->_bdd->echapper($titre) . ',';
        $requete .= '  nb_places=' . (int) $nb_places . ',';
        $requete .= '  date_debut=' . $this->_bdd->echapperSqlDateFromQuickForm($date_debut) . ',';
        $requete .= '  date_fin=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin) . ',';
        $requete .= '  annee=' . (int) $date_debut['Y'] . ',';
        $requete .= '  date_fin_appel_projet=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_appel_projet, true) . ',';
        $requete .= '  date_fin_appel_conferencier=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_appel_conferencier, true) . ',';
        $requete .= '  date_fin_vote=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_vote, false) . ',';
        $requete .= '  date_fin_prevente=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_prevente, true) . ',';
        $requete .= '  date_fin_vente=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_vente, true) . ',';
        $requete .= '  date_fin_vente_token_sponsor=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_vente_token_sponsor, true) . ',';
        $requete .= '  date_fin_saisie_repas_speakers=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_saisie_repas_speakers, true) . ',';
        $requete .= '  date_fin_saisie_nuites_hotel=' . $this->_bdd->echapperSqlDateFromQuickForm($date_fin_saisie_nuites_hotel, true) . ',';
        $requete .= '  date_annonce_planning=' . $this->_bdd->echapperSqlDateFromQuickForm($date_annonce_planning, true) . ',';
        $requete .= '  path=' . $this->_bdd->echapper($chemin_template) . ', ';
        $requete .= ' `text` = ' . $this->_bdd->echapper(json_encode($text)) . ', ';
        $requete .= ' `logo_url` = ' . $this->_bdd->echapper($logoUrl) . ', ';
        $requete .= ' `place_name` = ' . $this->_bdd->echapper($placeName) . ', ';
        $requete .= ' `vote_enabled` = ' . $this->_bdd->echapper($voteEnabled ? 1 : 0) . ', ';
        $requete .= ' `has_prices_defined_with_vat` = ' . $this->_bdd->echapper($hasPricesDefinedWithVat ? 1 : 0) . ', ';
        $requete .= ' `speakers_diner_enabled` = ' . $this->_bdd->echapper($speakersDinerEnabled ? 1 : 0) . ', ';
        $requete .= ' `accomodation_enabled` = ' . $this->_bdd->echapper($accomodationEnabled ? 1 : 0) . ', ';
        $requete .= ' `waiting_list_url` = ' . $this->_bdd->echapper($waitingListUrl) . ',';
        $requete .= ' `place_address` = ' . $this->_bdd->echapper($placeAddress) . ', ';
        $requete .= ' `transport_information_enabled` = ' . $this->_bdd->echapper($transportInformationEnabled ? 1 : 0) . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;

        return $this->_bdd->executer($requete);
    }

    public function supprimer($id_forum)
    {
        $id_forum = $this->_bdd->echapper($id_forum);

        $requete = 'DELETE FROM afup_forum WHERE id = ' . $id_forum;

        return $this->_bdd->executer($requete);
    }
}
