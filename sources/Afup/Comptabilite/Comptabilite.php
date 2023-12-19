<?php
//@TODO
// Ajout période comptable automatiquement
// revoir sous totaux balance
// test champ obligatoire lors de la saisie
// ajout filtre par mois pour les journaux banques
namespace Afup\Site\Comptabilite;

use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Compta\Importer\Importer;
use AppBundle\Compta\Importer\Operation;
use AppBundle\Model\ComptaCategorie;
use AppBundle\Model\ComptaEvenement;
use AppBundle\Model\ComptaModeReglement;

class Comptabilite
{
    /**
     * @var Base_De_Donnees
     */
    protected $_bdd;

    public $lastId = null;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }


    /* affiche le journal de :
     * courant = Compte courant
     * Livret A
     * Espece
     * Paypal
     *
     */
    function obtenirJournalBanque($compte = 1,
                                  $periode_debut = '',
                                  $periode_fin = ''
    )
    {

        $periode_debut = $this->periodeDebutFin($debutFin = 'debut', $periode_debut);
        $periode_fin = $this->periodeDebutFin($debutFin = 'fin', $periode_fin);
        $requete = 'SELECT ';
        $requete .= 'compta.date_regl, compta.description, compta.montant, compta.idoperation,  ';
        $requete .= 'MONTH(compta.date_regl) as mois, compta.id as idtmp, compta.comment,';
        $requete .= 'compta_reglement.reglement, ';
        $requete .= 'compta_evenement.evenement, compta.idevenement, ';
        $requete .= 'compta_categorie.categorie, compta.idcategorie, ';
        $requete .= 'compta.attachment_required, compta.attachment_filename, ';
        $requete .= 'compta_compte.nom_compte as compta_compte_nom_compte ';
        $requete .= 'FROM  ';
        $requete .= 'compta  ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_categorie on compta_categorie.id=compta.idcategorie ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_reglement on compta_reglement.id=compta.idmode_regl ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_evenement on compta_evenement.id=compta.idevenement ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_compte on compta_compte.id=compta.idcompte ';
        $requete .= 'WHERE  ';
        $requete .= 'compta.date_regl >= \'' . $periode_debut . '\' ';
        $requete .= 'AND compta.date_regl <= \'' . $periode_fin . '\'  ';
        $requete .= 'AND compta.montant != \'0.00\' ';
        $requete .= 'AND compta.idmode_regl = compta_reglement.id ';
        $requete .= 'AND idcompte = ' . (int)$compte . ' ';
        $requete .= 'ORDER BY ';
        $requete .= 'compta.date_regl ';
        return $this->_bdd->obtenirTous($requete);
    }


    function obtenirSousTotalJournalBanque($compte = 1, $periode_debut, $periode_fin)
    {

        $data = $this->obtenirJournalBanque($compte, $periode_debut, $periode_fin);

        for ($i = 1; $i <= 12; $i++) {
            $credit[$i] = '';
            $debit[$i] = '';
            $nligne[$i] = '';
        }
        foreach ($data as $id => $row) {
            if ($row['idoperation'] == "1") $debit[$row['mois']] += $row['montant'];
            if ($row['idoperation'] == "2") $credit[$row['mois']] += $row['montant'];
            if ($row['idoperation'] == "1" || $row['idoperation'] == "2") $nligne[$row['mois']]++;
        }

        $dif_old = 0;
        for ($i = 1; $i <= 12; $i++) {
            $dif = $dif_old + $credit[$i] - $debit[$i];
            $tableau[$i] = array("mois" => $i,
                "debit" => $debit[$i],
                "credit" => $credit[$i],
                "dif" => $dif,
                "nligne" => $nligne[$i]
            );
            $dif_old = $dif;
        }

        return $tableau;
    }

    function obtenirTotalJournalBanque($compte = 1, $periode_debut, $periode_fin)
    {

        $data = $this->obtenirJournalBanque($compte, $periode_debut, $periode_fin);
        /* echo "<pre>";
       print_r($data);
       echo "</pre>";*/
        $credit = 0;
        $debit = 0;

        foreach ($data as $id => $row) {
            if ($row['idoperation'] == "1") $debit += $row['montant'];
            if ($row['idoperation'] == "2") $credit += $row['montant'];
        }
//print_r($credit);
//$dif_old=0;
//for ($i=1;$i<=12;$i++)
//{
//	$dif=$dif_old+$credit[$i]-$debit[$i];
        $tableau = array(
            "debit" => $debit,
            "credit" => $credit,
            "dif" => $credit - $debit
        );
//	$dif_old=$dif;
//}

        return $tableau;
        /*		$total=0;
                foreach ($data as $id=>$row)
                {

                    if ($idoperation==$row['idoperation'])
                    $total += $row['montant'];
                }

                return $total;
                */

    }

    /* Journal des opération
     *
     */

    function obtenirJournal($debitCredit = '',
                            $periode_debut = '',
                            $periode_fin = '',
                            $onlyUnclasifedEntries = true
    )
    {

        $periode_debut = $this->periodeDebutFin($debutFin = 'debut', $periode_debut);
        $periode_fin = $this->periodeDebutFin($debutFin = 'fin', $periode_fin);

        if ($debitCredit == 1 || $debitCredit == 2)
            $filtre = 'AND compta.idoperation =\'' . $debitCredit . '\'  ';
        else
            $filtre = "";

        $requete = 'SELECT ';
        $requete .= 'compta.date_ecriture, compta.description, compta.montant, compta.idoperation,compta.id as idtmp, ';
        $requete .= 'compta.comment, compta.attachment_required, compta.attachment_filename, ';
        $requete .= 'compta_reglement.reglement, ';
        $requete .= 'compta_evenement.evenement, ';
        $requete .= 'compta_categorie.categorie, ';
        $requete .= 'compta_compte.nom_compte,    ';
        $requete .= '(COALESCE(compta.montant_ht_soumis_tva_0,0) + COALESCE(compta.montant_ht_soumis_tva_5_5,0) + COALESCE(compta.montant_ht_soumis_tva_10, 0) + COALESCE(compta.montant_ht_soumis_tva_20, 0)) as montant_ht,   ';
        $requete .= '((COALESCE(compta.montant_ht_soumis_tva_5_5, 0)*0.055) + (COALESCE(compta.montant_ht_soumis_tva_10, 0)*0.1) + (compta.montant_ht_soumis_tva_20*0.2)) as montant_tva,   ';
        $requete .= 'compta.montant_ht_soumis_tva_0 as montant_ht_0,   ';
        $requete .= 'compta.montant_ht_soumis_tva_5_5 as montant_ht_5_5,   ';
        $requete .= 'compta.montant_ht_soumis_tva_5_5*0.055 as montant_tva_5_5,   ';
        $requete .= 'compta.montant_ht_soumis_tva_10 as montant_ht_10,   ';
        $requete .= 'compta.montant_ht_soumis_tva_10*0.1 as montant_tva_10,   ';
        $requete .= 'compta.montant_ht_soumis_tva_20 as montant_ht_20,   ';
        $requete .= 'compta.montant_ht_soumis_tva_20*0.2 as montant_tva_20   ';
        $requete .= 'FROM ';
        $requete .= 'compta ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_categorie on compta_categorie.id=compta.idcategorie ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_reglement on compta_reglement.id=compta.idmode_regl ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_evenement on compta_evenement.id=compta.idevenement ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_compte on compta_compte.id=compta.idcompte ';
        $requete .= 'WHERE ';
        $requete .= ' compta.date_ecriture >= \'' . $periode_debut . '\' ';
        $requete .= 'AND compta.date_ecriture <= \'' . $periode_fin . '\'  ';
        $requete .= $filtre;
        if (true === $onlyUnclasifedEntries) {
            $requete .= ' AND (
                  compta_evenement.evenement = "A déterminer"
                OR
                  compta_categorie.categorie = "A déterminer"
                OR
                  compta_reglement.reglement = "A déterminer"
                OR
                  (compta.attachment_required = 1 AND compta.attachment_filename IS NULL)
            ) ';
        }
        $requete .= 'ORDER BY ';
        $requete .= 'compta.date_ecriture, numero_operation';

        return $this->_bdd->obtenirTous($requete);
    }

    // mise en forme du montant
    function formatMontantCompta($valeur)
    {
        $prix_ok = number_format($valeur, 2, ',', ' ');

        return $prix_ok;

    }

    function periodeDebutFin($debutFin = 'debut', $date = '')
    {
        // echo "=>$debutFin*$date*<br>";
        if ($date != '') {
            return $date;
        }


        if ($debutFin == 'debut') {
            /*			if ($id_periode !='')
                        {
                             $r=obtenirPeriodeEnCours($id_periode);
                        } else {*/
            return DATE("Y") . "-01-01";
            //		}
        } else {
            /*	if ($id_periode !='')
                {
                     $r=obtenirPeriodeEnCours($id_periode);
                     print_r($r);
                     return $r;
                } else {*/
            return DATE("Y") . "-12-31";
            //}
        }
    }

    function obtenirPeriodeEnCours($id_periode)
    {
        // Si la periode existe
        if ($id_periode != "") {
            return $id_periode;
        }

        // Sinon definir la periode en cours
        $date_debut = $this->periodeDebutFin('debut');
        $date_fin = $this->periodeDebutFin('fin');
        $result = $this->obtenirListPeriode($date_debut, $date_fin);

        if ($result) {
            return $result[0]['id'];
        } else                // ajout d'une nouvelle periode
        {
            $result = $this->ajouterListPeriode();
            return $result[0]['id'];
        }
    }

    function ajouterListPeriode()
    {

        $date_debut = DATE("Y") . '-01-01';
        $date_fin = DATE("Y") . '-12-31';

        $requete = 'INSERT INTO ';
        $requete .= 'compta_periode (';
        $requete .= 'date_debut,date_fin,verouiller) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($date_debut) . ',';
        $requete .= $this->_bdd->echapper($date_fin) . ',';
        $requete .= $this->_bdd->echapper(0) . ' ';
        $requete .= ');';

        $this->_bdd->executer($requete);
        return $this->obtenirListPeriode($date_debut, $date_fin);

    }

    function obtenirListPeriode($date_debut = '', $date_fin = '')
    {
        $requete = 'SELECT ';
        $requete .= 'id, date_debut,date_fin, verouiller ';
        $requete .= 'FROM  ';
        $requete .= 'compta_periode  ';

        if ($date_debut != '' AND $date_fin != '') {
            $requete .= 'WHERE ';
            $requete .= 'compta_periode.date_debut= \'' . $date_debut . '\'  ';
            $requete .= 'AND compta_periode.date_fin= \'' . $date_fin . '\'  ';
        }

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListOperations($filtre = '', $where = '')
    {
        $requete = 'SELECT ';
        $requete .= 'id, operation ';
        $requete .= 'FROM  ';
        $requete .= 'compta_operation  ';
        if ($where) $requete .= 'WHERE id=' . $where . ' ';

        $requete .= 'ORDER BY ';
        $requete .= 'operation ';

        if ($where) {
            return $this->_bdd->obtenirEnregistrement($requete);
        } elseif ($filtre) {
            return $this->_bdd->obtenirTous($requete);
        } else {
            $data = $this->_bdd->obtenirTous($requete);
            $result[] = "";
            foreach ($data as $row) {
                $result[$row['id']] = $row['operation'];
            }

            return $result;
        }
    }

    function obtenirListComptes($filtre = '', $where = '')
    {
        $requete = 'SELECT ';
        $requete .= 'id, nom_compte ';
        $requete .= 'FROM  ';
        $requete .= 'compta_compte  ';
        if ($where) $requete .= 'WHERE id=' . $where . ' ';

        $requete .= 'ORDER BY ';
        $requete .= 'nom_compte ';

        if ($where) {
            return $this->_bdd->obtenirEnregistrement($requete);
        } elseif ($filtre) {
            return $this->_bdd->obtenirTous($requete);
        } else {
            $data = $this->_bdd->obtenirTous($requete);
            $result[] = "";
            foreach ($data as $row) {
                $result[$row['id']] = $row['nom_compte'];
            }

            return $result;
        }
    }

    function obtenirListCategories($filtre = '', $where = '', $usedInAccountingJournal = false)
    {
        $requete = 'SELECT ';
        $requete .= 'id, idevenement, categorie ';
        $requete .= 'FROM  ';
        $requete .= 'compta_categorie  ';
        $wheres = [];
        if ($where) {
            $wheres[] = 'id=' . $where . ' ';
        }
        if ($usedInAccountingJournal) {
            $wheres[] = 'hide_in_accounting_journal_at IS NULL';
        }

        if (count($wheres)) {
            $requete .= sprintf('WHERE %s ',implode(' AND ', $wheres));
        }

        $requete .= 'ORDER BY ';
        $requete .= 'categorie ';

        if ($where) {
            return $this->_bdd->obtenirEnregistrement($requete);
        } elseif ($filtre) {
            return $this->_bdd->obtenirTous($requete);
        } else {
            $data = $this->_bdd->obtenirTous($requete);
            $result[] = "";
            foreach ($data as $row) {
                $result[$row['id']] = $row['categorie'];
            }

            return $result;
        }

    }

    public function obtenirListCategoriesJournal()
    {
        $categories = $this->obtenirListCategories('', '', true);
        unset($categories[0]);

        return $categories;
    }

    function obtenirListEvenements($filtre = '', $where = '', $usedInAccountingJournal = false)
    {
        $requete = 'SELECT ';
        $requete .= 'id, evenement ';
        $requete .= 'FROM  ';
        $requete .= 'compta_evenement  ';
        $wheres = [];
        if ($where) {
            $wheres[] = 'id=' . $where . ' ';
        }
        if ($usedInAccountingJournal) {
            $wheres[] = 'hide_in_accounting_journal_at IS NULL';
        }

        if (count($wheres)) {
            $requete .= sprintf('WHERE %s ',implode(' AND ', $wheres));
        }

        $requete .= 'ORDER BY ';
        $requete .= 'evenement ';

        if ($where) {
            return $this->_bdd->obtenirEnregistrement($requete);
        } elseif ($filtre) {
            return $this->_bdd->obtenirTous($requete);
        } else {
            $data = $this->_bdd->obtenirTous($requete);
            $result[] = "";
            foreach ($data as $row) {
                $result[$row['id']] = $row['evenement'];
            }

            return $result;
        }
    }

    public function obtenirListEvenementsJournal()
    {
        $events = $this->obtenirListEvenements('', '', true);
        unset($events[0]);

        return $events;
    }

    function obtenirListReglements($filtre = '', $where = '', $usedInAccountingJournal = false)
    {
        $requete = 'SELECT ';
        $requete .= 'id, reglement ';
        $requete .= 'FROM  ';
        $requete .= 'compta_reglement  ';
        $wheres = [];
        if ($where) {
            $wheres[] = 'id=' . $where . ' ';
        }
        if ($usedInAccountingJournal) {
            $wheres[] = 'hide_in_accounting_journal_at IS NULL';
        }

        if (count($wheres)) {
            $requete .= sprintf('WHERE %s ',implode(' AND ', $wheres));
        }

        $requete .= 'ORDER BY ';
        $requete .= 'reglement ';

        if ($where) {
            return $this->_bdd->obtenirEnregistrement($requete);
        } elseif ($filtre) {
            return $this->_bdd->obtenirTous($requete);
        } else {
            $data = $this->_bdd->obtenirTous($requete);
            $result[] = "";
            foreach ($data as $row) {
                $result[$row['id']] = $row['reglement'];
            }

            return $result;
        }
    }

    public function obtenirListReglementsJournal()
    {
        $reglements = $this->obtenirListReglements('','', true);
        unset($reglements[0]);

        return $reglements;
    }

    function ajouter($idoperation, $idcompte, $idcategorie, $date_ecriture, $nom_frs, $tva_intra, $montant, $description,
                     $numero, $idmode_regl, $date_regl, $obs_regl, $idevenement, $numero_operation = null,
                     $attachmentRequired = 0, $montantHtSoumisTva0 = null, $montantHtSoumisTva55 = null, $montantHtSoumisTva10 = null, $montantHtSoumisTva20 = null)
    {

        $requete = 'INSERT INTO ';
        $requete .= 'compta (';
        $requete .= 'idoperation,idcategorie,date_ecriture,nom_frs,tva_intra,montant,description,';
        $requete .= 'numero,idmode_regl,date_regl,obs_regl,idevenement, numero_operation,idcompte, attachment_required,
        montant_ht_soumis_tva_0, montant_ht_soumis_tva_5_5, montant_ht_soumis_tva_10, montant_ht_soumis_tva_20
        ) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($idoperation) . ',';
        $requete .= $this->_bdd->echapper($idcategorie) . ',';
        $requete .= $this->_bdd->echapper($date_ecriture) . ',';
        $requete .= $this->_bdd->echapper($nom_frs) . ',';
        $requete .= $this->_bdd->echapper($tva_intra) . ',';
        $requete .= $this->_bdd->echapper($montant) . ',';
        $requete .= $this->_bdd->echapper($description) . ',';
        $requete .= $this->_bdd->echapper($numero) . ',';
        $requete .= $this->_bdd->echapper($idmode_regl) . ',';
        $requete .= $this->_bdd->echapper($date_regl) . ',';
        $requete .= $this->_bdd->echapper($obs_regl) . ',';
        $requete .= $this->_bdd->echapper($idevenement) . ',';
        $requete .= $this->_bdd->echapper($numero_operation) . ',';
        $requete .= $this->_bdd->echapper($idcompte) . ',';
        $requete .= $this->_bdd->echapper($attachmentRequired) . ',';
        $requete .= (!$montantHtSoumisTva0 ? 'NULL' : $this->_bdd->echapper($montantHtSoumisTva0)) . ',';
        $requete .= (!$montantHtSoumisTva55 ? 'NULL' : $this->_bdd->echapper($montantHtSoumisTva55)) . ',';
        $requete .= (!$montantHtSoumisTva10 ? 'NULL' : $this->_bdd->echapper($montantHtSoumisTva10)) . ',';
        $requete .= (!$montantHtSoumisTva20 ? 'NULL' : $this->_bdd->echapper($montantHtSoumisTva20)) . '';
        $requete .= ');';

        $resultat = $this->_bdd->executer($requete);
        if ($resultat) {
            $this->lastId = $this->_bdd->obtenirDernierId();
        }
        return $resultat;
    }

    function modifier($id, $idoperation, $idcompte, $idcategorie, $date_ecriture, $nom_frs, $tva_intra, $montant, $description,
                      $numero, $idmode_regl, $date_regl, $obs_regl, $idevenement, $comment, $numero_operation = null, $attachmentRequired = 0,
                      $montantHtSoumisTva0 = null, $montantHtSoumisTva55 = null, $montantHtSoumisTva10 = null, $montantHtSoumisTva20 = null
    )
    {

        $requete = 'UPDATE ';
        $requete .= 'compta ';
        $requete .= 'SET ';
        $requete .= 'idoperation=' . $this->_bdd->echapper($idoperation) . ',';
        $requete .= 'idcategorie=' . $this->_bdd->echapper($idcategorie) . ',';
        $requete .= 'date_ecriture=' . $this->_bdd->echapper($date_ecriture) . ',';
        $requete .= 'nom_frs=' . $this->_bdd->echapper($nom_frs) . ',';
        $requete .= 'tva_intra=' . $this->_bdd->echapper($tva_intra) . ',';
        $requete .= 'montant=' . $this->_bdd->echapper($montant) . ',';
        $requete .= 'description=' . $this->_bdd->echapper($description) . ',';
        $requete .= 'numero=' . $this->_bdd->echapper($numero) . ',';
        $requete .= 'idmode_regl=' . $this->_bdd->echapper($idmode_regl) . ',';
        $requete .= 'date_regl=' . $this->_bdd->echapper($date_regl) . ',';
        $requete .= 'obs_regl=' . $this->_bdd->echapper($obs_regl) . ',';
        $requete .= 'idcompte=' . $this->_bdd->echapper($idcompte) . ',';
        $requete .= 'montant_ht_soumis_tva_0=' . (!$montantHtSoumisTva0 ? 'NULL' : $this->_bdd->echapper($montantHtSoumisTva0)) . ',';
        $requete .= 'montant_ht_soumis_tva_5_5=' . (!$montantHtSoumisTva55 ? 'NULL' : $this->_bdd->echapper($montantHtSoumisTva55)) . ',';
        $requete .= 'montant_ht_soumis_tva_10=' . (!$montantHtSoumisTva10 ? 'NULL' : $this->_bdd->echapper($montantHtSoumisTva10)) . ',';
        $requete .= 'montant_ht_soumis_tva_20=' . (!$montantHtSoumisTva20 ? 'NULL' : $this->_bdd->echapper($montantHtSoumisTva20)) . ',';
        $requete .= 'comment=' . (!$comment ? 'NULL' : $this->_bdd->echapper($comment)) . ',';
        if ($numero_operation) {
            $requete .= 'numero_operation=' . $this->_bdd->echapper($numero_operation) . ',';
        }
        $requete .= 'idevenement=' . $this->_bdd->echapper($idevenement) . ',';
        $requete .= 'attachment_required=' . $this->_bdd->echapper($attachmentRequired) . ' ';
        $requete .= 'WHERE ';
        $requete .= 'id=' . $id . ' ';

        return $this->_bdd->executer($requete);
    }

    /**
     * Update one column value of a "compta" line.
     * @param $id int "compta" identifier
     * @param $columnName string Column name (whitelist: idcategorie, idmode_regl, idevenement)
     * @param $columnValue string New value
     * @return \mysqli_result|bool FALSE on failure
     * @throws \Exception If bad column name
     */
    public function modifierColonne($id, $columnName, $columnValue)
    {

        // Check column using whitelist
        if (!in_array($columnName, [
            'idcategorie',
            'idmode_regl',
            'idevenement',
            'comment',
            'attachment_required',
            'attachment_filename',
        ])
        ) {
            throw new \Exception("Please provide a whitelisted column name.");
        }

        $id = intval($id);
        $requete = <<<SQL
UPDATE compta
	SET $columnName = {$this->_bdd->echapper($columnValue)}
	WHERE id = {$id};
SQL;

        return $this->_bdd->executer($requete);
    }

    function ajouterConfig($table, $champ, $valeur)
    {
        $requete = 'INSERT INTO ';
        $requete .= '' . $table . ' (';
        $requete .= '' . $champ . ') ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($valeur) . ' ';
        $requete .= ');';

        return $this->_bdd->executer($requete);
    }

    function modifierConfig($table, $id, $champ, $valeur)
    {

        $requete = 'UPDATE ';
        $requete .= '' . $table . ' ';
        $requete .= 'SET ';
        $requete .= '' . $champ . ' = ' . $this->_bdd->echapper($valeur) . ' ';
        $requete .= 'WHERE ';
        $requete .= 'id = ' . $id . ' ';

        return $this->_bdd->executer($requete);
    }

    function obtenir($id)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';
        $requete .= 'WHERE id=' . $id;

        return $this->_bdd->obtenirEnregistrement($requete);
    }


    function obtenirSyntheseEvenement($idoperation = '1', $idevenement)
    {
        $requete = 'SELECT ';
        $requete .= 'compta.*, ';
        $requete .= 'compta_categorie.id, compta_categorie.categorie   ';
        $requete .= 'FROM  ';
        $requete .= 'compta,  ';
        $requete .= 'compta_categorie ';
        $requete .= 'WHERE  ';
        $requete .= 'compta.idevenement = \'' . $idevenement . '\' ';
        $requete .= 'AND compta.idoperation = \'' . $idoperation . '\' ';
        $requete .= 'AND compta.idcategorie = compta_categorie.id ';
        $requete .= 'ORDER BY ';
        $requete .= 'compta_categorie.categorie, ';
        $requete .= 'compta.date_ecriture ';

        return $this->_bdd->obtenirTous($requete);

    }


    function obtenirTotalSyntheseEvenement($idoperation = '1', $idevenement)
    {
        $requete = 'SELECT ';
        $requete .= 'compta.montant ';
        $requete .= 'FROM  ';
        $requete .= 'compta  ';
        $requete .= 'WHERE  ';
        $requete .= 'compta.idevenement = \'' . $idevenement . '\' ';
        $requete .= 'AND compta.idoperation = \'' . $idoperation . '\' ';

        $data = $this->_bdd->obtenirTous($requete);

        $total = 0;
        foreach ($data as $id => $row) {
            $total += $row['montant'];
        }
        return $total;
    }

    function obtenirBilan($idoperation = '1', $periode_debut = '', $periode_fin = '')
    {
        $periode_debut = $this->periodeDebutFin($debutFin = 'debut', $periode_debut);
        $periode_fin = $this->periodeDebutFin($debutFin = 'fin', $periode_fin);

        $requete = 'SELECT ';
        $requete .= ' SUM(compta.montant) as montant, ';
        $requete .= ' compta_evenement.id, compta_evenement.evenement   ';
        $requete .= 'FROM  ';
        $requete .= ' compta,  ';
        $requete .= ' compta_evenement ';
        $requete .= 'WHERE  ';
        $requete .= ' compta.idoperation = \'' . $idoperation . '\' ';
        $requete .= ' AND compta.date_ecriture >= \'' . $periode_debut . '\' ';
        $requete .= ' AND compta.date_ecriture <= \'' . $periode_fin . '\'  ';
        $requete .= ' AND compta.idevenement = compta_evenement.id ';
        $requete .= 'GROUP BY';
        $requete .= ' compta_evenement.evenement ';
        $requete .= 'ORDER BY ';
        $requete .= ' compta_evenement.evenement ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirTotalBilan($idoperation = '1', $periode_debut, $periode_fin)
    {

        $data = $this->obtenirBilan($idoperation, $periode_debut, $periode_fin);

        $total = 0;
        foreach ($data as $id => $row) {

            $total += $row['montant'];
        }

        return $total;
    }

    function obtenirBilanDetails($idoperation, $periode_debut = '', $periode_fin = '', $idevenement)
    {
        $periode_debut = $this->periodeDebutFin($debutFin = 'debut', $periode_debut);
        $periode_fin = $this->periodeDebutFin($debutFin = 'fin', $periode_fin);

        $requete = 'SELECT ';
        $requete .= ' IF( compta.idoperation =1, compta.montant, "" )  AS debit, ';
        $requete .= ' IF( compta.idoperation =2, compta.montant, "" )  AS credit, ';
        $requete .= ' compta.date_ecriture, compta.description, ';
        $requete .= ' montant, ';
        $requete .= ' compta.id as compta_id, ';
        $requete .= ' compta_evenement.id, compta_evenement.evenement   ';
        $requete .= 'FROM  ';
        $requete .= ' compta,  ';
        $requete .= ' compta_evenement ';
        $requete .= 'WHERE  ';
        $requete .= ' compta.idoperation = \'' . $idoperation . '\' ';
        $requete .= ' AND compta.date_ecriture >= \'' . $periode_debut . '\' ';
        $requete .= ' AND compta.date_ecriture <= \'' . $periode_fin . '\'  ';
        $requete .= ' AND compta.idevenement = compta_evenement.id ';
        $requete .= ' AND compta.idevenement = \'' . $idevenement . '\' ';
        //$requete .= 'GROUP BY';
        //$requete .= ' compta_evenement.evenement ';
        $requete .= 'ORDER BY ';
        $requete .= ' compta.date_ecriture ';
//echo $requete."<br>";
        return $this->_bdd->obtenirTous($requete);

    }

    function obtenirSousTotalBilan($idoperation = '1', $periode_debut, $periode_fin, $idevenement)
    {

        $data = $this->obtenirBilanDetails($idoperation, $periode_debut, $periode_fin, $idevenement);

        $total = 0;
        foreach ($data as $id => $row) {

            $total += $row['montant'];
        }

        return $total;
    }


    function obtenirBalance($idoperation = '', $periode_debut = '', $periode_fin = '')
    {
        $periode_debut = $this->periodeDebutFin($debutFin = 'debut', $periode_debut);
        $periode_fin = $this->periodeDebutFin($debutFin = 'fin', $periode_fin);

        $requete = 'SELECT ';
        $requete .= ' SUM( IF( compta.idoperation = 1, compta.montant, "" ) ) AS debit, ';
        $requete .= ' SUM( IF( compta.idoperation = 2, compta.montant, "" ) ) AS credit, ';
        $requete .= ' compta.date_ecriture,compta.montant,compta.idoperation, compta.idevenement, compta.id as idtmp, ';
        $requete .= ' compta_evenement.id,compta_evenement.evenement ';
        $requete .= 'FROM  ';
        $requete .= ' compta,  ';
        $requete .= ' compta_evenement ';
        $requete .= 'WHERE  ';
        $requete .= ' compta.idevenement = compta_evenement.id ';
        $requete .= ' AND compta.date_ecriture >= \'' . $periode_debut . '\' ';
        $requete .= ' AND compta.date_ecriture <= \'' . $periode_fin . '\'  ';
        if ($idoperation != '')
            $requete .= ' AND compta.idoperation = \'' . $idoperation . '\' ';

        $requete .= 'GROUP BY ';
        $requete .= ' compta_evenement.evenement ';
        $requete .= 'ORDER BY  ';
        $requete .= ' compta_evenement.evenement ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirTotalBalance($idoperation = '1', $periode_debut, $periode_fin)
    {

        $data = $this->obtenirBalance($idoperation, $periode_debut, $periode_fin);

        $total = 0;
        foreach ($data as $id => $row) {
            if ($idoperation == 1) $total += $row['debit'];
            if ($idoperation == 2) $total += $row['credit'];

        }
        return $total;
    }

    function obtenirBalanceDetails($evenement, $periode_debut = '', $periode_fin = '')
    {
        $periode_debut = $this->periodeDebutFin($debutFin = 'debut', $periode_debut);
        $periode_fin = $this->periodeDebutFin($debutFin = 'fin', $periode_fin);


        $requete = 'SELECT ';
        $requete .= ' IF( compta.idoperation =1, compta.montant, "" )  AS debit, ';
        $requete .= ' IF( compta.idoperation =2, compta.montant, "" )  AS credit, ';
        $requete .= 'compta.description, compta.id as idtmp, ';
        $requete .= ' compta.date_ecriture,compta.montant,compta.idoperation, compta.idevenement, ';
        $requete .= ' compta_categorie.id,compta_categorie.categorie ';
        $requete .= 'FROM  ';
        $requete .= ' compta,  ';
        $requete .= ' compta_categorie ';
        $requete .= 'WHERE  ';
        $requete .= ' compta.idcategorie = compta_categorie.id ';
        $requete .= ' AND compta.date_ecriture >= \'' . $periode_debut . '\' ';
        $requete .= ' AND compta.date_ecriture <= \'' . $periode_fin . '\'  ';
        $requete .= ' AND compta.idevenement = \'' . $evenement . '\' ';
        $requete .= 'ORDER BY  ';
        $requete .= ' compta_categorie.categorie,compta.date_ecriture ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirSousTotalBalance($evenement, $periode_debut, $periode_fin)
    {
        $tableau = [];
//	    	echo $evenement."*".$periode_debut."*".$periode_fin;
        $data = $this->obtenirBalanceDetails($evenement, $periode_debut, $periode_fin);

        for ($i = 1; $i <= 30; $i++) {
            $credit[$i] = '';
            $debit[$i] = '';
            $nligne[$i] = 0;

        }
        foreach ($data as $id => $row) {
            if ($row['idoperation'] == "1") $debit[$row['id']] += $row['montant'];
            if ($row['idoperation'] == "2") $credit[$row['id']] += $row['montant'];
            if ($row['idoperation'] == "1" || $row['idoperation'] == "2") $nligne[$row['id']]++;

        }


        for ($i = 1; $i <= 30; $i++) {
            if ($debit[$i] || $credit[$i]) {
                $tableau[$i] = array("idevenement" => $i,
                    "debit" => $debit[$i],
                    "credit" => $credit[$i],
                    "nligne" => $nligne[$i]
                );
            }
        }

        return $tableau;
    }

    function supprimerEcriture($id)
    {
        $requete = 'DELETE FROM compta WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }

    function obtenirParNumeroOperation($numero_operation)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';
        $requete .= 'WHERE numero_operation=' . $this->_bdd->echapper($numero_operation);

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirSuivantADeterminer($numero_operation)
    {
        $requete = 'SELECT';
        $requete .= '  id ';
        $requete .= 'FROM';
        $requete .= '  compta ';
        $requete .= 'WHERE ';
        $requete .= '  (';
        $requete .= '    idcategorie = 26 ';
        $requete .= '      OR ';
        $requete .= '    idevenement = 8';
        $requete .= '   )';
        $requete .= ' AND id > ' . $this->_bdd->echapper($numero_operation);
        $requete .= ' LIMIT 1;';
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirTous()
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirEvenementParIdForum($id)
    {
        $requete = 'SELECT ';
        $requete .= '  compta_evenement.id ';
        $requete .= 'FROM ';
        $requete .= '  compta_evenement ';
        $requete .= 'INNER JOIN ';
        $requete .= '  afup_forum on afup_forum.titre = compta_evenement.evenement ';
        $requete .= 'WHERE ';
        $requete .= '  afup_forum.id = ' . (int)$id;
        return $this->_bdd->obtenirUn($requete);
    }

    /**
     *
     * @param Importer $importer
     * @return bool
     */
    function extraireComptaDepuisCSVBanque(Importer $importer)
    {
        if (!$importer->validate()) {
            return false;
        }

        foreach ($importer->extract() as $operation) {
            $numero_operation = $operation->getNumeroOperation();
            // On vérife si l'enregistrement existe déjà
            $enregistrement = $this->obtenirParNumeroOperation($numero_operation);

            $date_ecriture = $operation->getDateEcriture();
            $description = $operation->getDescription();
            $idoperation = $operation->isCredit() ? 2 : 1;
            $montant = $operation->getMontant();

            // On tente les préaffectations
            $categorie = 26; // Catégorie 26 = "A déterminer"
            $evenement = 8;  // Événement 8 = "A déterminer"

            $idModeReglement = 9;
            $attachmentRequired = 0;

            $firstPartDescription = strtoupper(explode(' ', $description)[0]);
            switch ($firstPartDescription) {
                case 'CB':
                    $idModeReglement = ComptaModeReglement::CB;
                    break;
                case 'VIR':
                    $idModeReglement = ComptaModeReglement::VIREMENT;
                    break;
                case 'CHE':
                case 'REM':
                    $idModeReglement = ComptaModeReglement::CHEQUE;
                    break;
                case 'PRLV':
                    $idModeReglement = ComptaModeReglement::PRELEVEMENT;
                    break;
            }

            if ($operation->isCredit()) {
                if (0 === strpos($description, 'VIR SEPA sprd.net AG')) {
                    $evenement = ComptaEvenement::ASSOCIATION_AFUP;
                    $categorie = ComptaCategorie::GOODIES;
                    $attachmentRequired = 1;
                }
            } else {
                if (0 === strpos($description, '*CB COM AFUP ')) {
                    $idModeReglement = ComptaModeReglement::PRELEVEMENT;
                    $evenement = ComptaEvenement::GESTION;
                    $categorie = ComptaCategorie::FRAIS_DE_COMPTE;
                }

                if (0 === strpos($description, '* COTIS ASSOCIATIS ESSENTIEL')) {
                    $idModeReglement = ComptaModeReglement::PRELEVEMENT;
                    $evenement = ComptaEvenement::GESTION;
                    $categorie = ComptaCategorie::FRAIS_DE_COMPTE;
                }

                if (0 === strpos(strtoupper($description), 'PRLV URSSAF')) {
                    $evenement = ComptaEvenement::GESTION;
                    $categorie = ComptaCategorie::CHARGES_SOCIALES;
                }

                if ($description === 'PRLV B2B DGFIP') {
                    $evenement = ComptaEvenement::GESTION;
                    $categorie = ComptaCategorie::PRELEVEMENT_SOURCE;
                }

                if (0 === strpos($description, 'PRLV A3M - RETRAITE - MALAKOFF HUMANIS')) {
                    $evenement = ComptaEvenement::GESTION;
                    $categorie = ComptaCategorie::CHARGES_SOCIALES;
                }

                if (0 === strpos($description, 'PRLV Online SAS -')) {
                    $evenement = ComptaEvenement::ASSOCIATION_AFUP;
                    $categorie = ComptaCategorie::OUTILS;
                    $attachmentRequired = 1;
                }

                if (0 === strpos($description, 'CB MEETUP ORG')) {
                    $evenement = ComptaEvenement::ASSOCIATION_AFUP;
                    $categorie = ComptaCategorie::MEETUP;
                    $attachmentRequired = 1;
                }

                if (0 === strpos($description, 'PRLV POINT TRANSACTION SYSTEM -')) {
                    $evenement = ComptaEvenement::GESTION;
                    $categorie = ComptaCategorie::FRAIS_DE_COMPTE;
                    $attachmentRequired = 1;
                }

                if (0 === strpos(strtoupper($description), 'CB MAILCHIMP FACT')) {
                    $evenement = ComptaEvenement::ASSOCIATION_AFUP;
                    $categorie = ComptaCategorie::MAILCHIMP;
                    $attachmentRequired = 1;
                }

                if (0 === strpos($description, 'CB AWS EMEA FACT')) {
                    $evenement = ComptaEvenement::ASSOCIATION_AFUP;
                    $categorie = ComptaCategorie::OUTILS;
                    $attachmentRequired = 1;
                }

                if (0 === strpos($description, 'CB GANDI FACT')) {
                    $evenement = ComptaEvenement::ASSOCIATION_AFUP;
                    $categorie = ComptaCategorie::GANDI;
                    $attachmentRequired = 1;
                }

                if (0 === strpos($description, 'CB Twilio')) {
                    $evenement = ComptaEvenement::ASSOCIATION_AFUP;
                    $categorie = ComptaCategorie::OUTILS;
                    $attachmentRequired = 1;
                }
            }

            if (!is_array($enregistrement)) {
                $this->ajouter(
                    $idoperation,
                    $importer->getCompteId(),
                    $categorie,
                    $date_ecriture,
                    '',
                    $montant,
                    $description,
                    '',
                    $idModeReglement,
                    $date_ecriture,
                    '',
                    $evenement,
                    $numero_operation,
                    $attachmentRequired
                );
            } else {
                $modifier = false;
                if ($enregistrement['idcategorie'] == 26 && $categorie != 26) {
                    $enregistrement['idcategorie'] = $categorie;
                    $modifier = true;
                }
                if ($enregistrement['idevenement'] == 8 && $evenement != 8) {
                    $enregistrement['idevenement'] = $evenement;
                    $modifier = true;
                }
                if ($modifier) {
                    $this->modifier($enregistrement['id'],
                        $enregistrement['idoperation'],
                        $importer->getCompteId(),
                        $enregistrement['idcategorie'],
                        $enregistrement['date_ecriture'],
                        $enregistrement['nom_frs'],
                        $enregistrement['tva_intra'],
                        $enregistrement['montant'],
                        $enregistrement['description'],
                        $enregistrement['numero'],
                        $enregistrement['idmode_regl'],
                        $enregistrement['date_regl'],
                        $enregistrement['obs_regl'],
                        $enregistrement['idevenement'],
                        $enregistrement['numero_operation'],
                        $attachmentRequired
                    );
                }
            }
        }
        return true;
    }

    /**
     * Search in whole database
     * <p>We do multiple queries.</p>
     * @param $query string String to search
     * @return array Results sorted by type
     */
    public function rechercher($query)
    {
        $like = $this->_bdd->echapper("%$query%");
        $results = [];

        // "cotisations" for companies
        $select = <<<SQL
SELECT pers.nom, pers.prenom, pers.email, pers.raison_sociale, cotis.*
  FROM afup_cotisations AS cotis
  LEFT JOIN afup_personnes_morales AS pers
    ON pers.id = cotis.id_personne
  WHERE
    cotis.type_personne = 1
    AND (
      cotis.informations_reglement LIKE $like
      OR cotis.numero_facture LIKE $like
      OR cotis.commentaires LIKE $like
      OR pers.email LIKE $like
      OR pers.nom LIKE $like
      OR pers.prenom LIKE $like
    )
  ;
SQL;

        if ($cotisations = $this->_bdd->obtenirTous($select, MYSQLI_ASSOC)) {
            $results['cotisations_personnes_morales'] = $cotisations;
        }

        // "cotisations" for people
        $select = <<<SQL
SELECT pers.nom, pers.prenom, pers.email, pers.login, cotis.*
  FROM afup_cotisations AS cotis
  LEFT JOIN afup_personnes_physiques AS pers
    ON pers.id = cotis.id_personne
  WHERE
    cotis.type_personne = 0
    AND (
      cotis.informations_reglement LIKE $like
      OR cotis.numero_facture LIKE $like
      OR cotis.commentaires LIKE $like
      OR pers.login LIKE $like
      OR pers.email LIKE $like
      OR pers.nom LIKE $like
      OR pers.prenom LIKE $like
    )
  ;
SQL;

        if ($cotisations = $this->_bdd->obtenirTous($select, MYSQLI_ASSOC)) {
            $results['cotisations_personnes_physiques'] = $cotisations;
        }

        // Forum registrations
        $select = <<<SQL
SELECT insc.*, forum.titre AS forum_titre
  FROM afup_inscription_forum AS insc
  LEFT JOIN afup_forum AS forum ON insc.id_forum = forum.id
  WHERE
    insc.reference LIKE $like
    OR insc.informations_reglement LIKE $like
    OR insc.commentaires LIKE $like
    OR insc.nom LIKE $like
    OR insc.prenom LIKE $like
    OR insc.email LIKE $like
  ;
SQL;
        if ($registrations = $this->_bdd->obtenirTous($select, MYSQLI_ASSOC)) {
            $results['forum_inscriptions'] = $registrations;
        }

        // Forum invoicing
        $select = <<<SQL
SELECT inv.*, forum.titre AS forum_titre
  FROM afup_facturation_forum AS inv
  LEFT JOIN afup_forum AS forum ON inv.id_forum = forum.id
  WHERE
    inv.reference LIKE $like
    OR inv.informations_reglement LIKE $like
    OR inv.email LIKE $like
    OR inv.societe LIKE $like
    OR inv.nom LIKE $like
    OR inv.prenom LIKE $like
    OR inv.autorisation LIKE $like
    OR inv.transaction LIKE $like
  ;
SQL;
        if ($invoices = $this->_bdd->obtenirTous($select, MYSQLI_ASSOC)) {
            $results['forum_factures'] = $invoices;
        }

        // Global invoicing
        $select = <<<SQL
SELECT inv.*, SUM(det.pu * det.quantite) AS total,
    GROUP_CONCAT(det.ref SEPARATOR ', ') AS refs,
    GROUP_CONCAT(det.designation SEPARATOR ', ') AS details
  FROM afup_compta_facture AS inv
  LEFT JOIN afup_compta_facture_details AS det
    ON det.idafup_compta_facture = inv.id AND det.quantite > 0
  WHERE
    inv.numero_devis LIKE $like
    OR inv.numero_facture LIKE $like
    OR inv.societe LIKE $like
    OR inv.service LIKE $like
    OR inv.email LIKE $like
    OR inv.ref_clt1 LIKE $like
    OR inv.ref_clt2 LIKE $like
    OR inv.ref_clt3 LIKE $like
    OR inv.observation LIKE $like
  GROUP BY inv.id
  ;
SQL;
        if ($invoices = $this->_bdd->obtenirTous($select, MYSQLI_ASSOC)) {
            $results['factures'] = $invoices;
        }

        return $results;
    }

}

