<?php

declare(strict_types=1);
//@TODO
// Ajout période comptable automatiquement
// revoir sous totaux balance
// test champ obligatoire lors de la saisie
// ajout filtre par mois pour les journaux banques
namespace Afup\Site\Comptabilite;

use Afup\Site\Forum\Forum;
use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Compta\Importer\AutoQualifier;
use AppBundle\Compta\Importer\Importer;

class Comptabilite
{
    const TVA_ZONES = [
        'france' => 'France',
        'ue' => 'Union Européenne hors France',
        'hors_ue' => 'Hors Union Européenne',
    ];

    public $lastId;

    public function __construct(protected Base_De_Donnees $_bdd)
    {
    }


    /* affiche le journal de :
     * courant = Compte courant
     * Livret A
     * Espece
     * Paypal
     *
     */
    public function obtenirJournalBanque($compte = 1,
                                  $periode_debut = '',
                                  $periode_fin = '',
    ) {
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
        $requete .= 'AND idcompte = ' . (int) $compte . ' ';
        $requete .= 'ORDER BY ';
        $requete .= 'compta.date_regl ';
        return $this->_bdd->obtenirTous($requete);
    }


    public function obtenirSousTotalJournalBanque($periode_debut, $periode_fin, $compte = 1)
    {
        $data = $this->obtenirJournalBanque($compte, $periode_debut, $periode_fin);

        for ($i = 1; $i <= 12; $i++) {
            $credit[$i] = 0;
            $debit[$i] = 0;
            $nligne[$i] = 0;
        }
        foreach ($data as $row) {
            if ($row['idoperation'] == "1") {
                $debit[$row['mois']] += $row['montant'];
            }
            if ($row['idoperation'] == "2") {
                $credit[$row['mois']] += $row['montant'];
            }
            if ($row['idoperation'] == "1" || $row['idoperation'] == "2") {
                $nligne[$row['mois']]++;
            }
        }

        $dif_old = 0;
        for ($i = 1; $i <= 12; $i++) {
            $dif = $dif_old + $credit[$i] - $debit[$i];
            $tableau[$i] = ["mois" => $i,
                "debit" => $debit[$i],
                "credit" => $credit[$i],
                "dif" => $dif,
                "nligne" => $nligne[$i],
            ];
            $dif_old = $dif;
        }

        return $tableau;
    }

    public function obtenirTotalJournalBanque($periode_debut, $periode_fin, $compte = 1): array
    {
        $data = $this->obtenirJournalBanque($compte, $periode_debut, $periode_fin);
        /* echo "<pre>";
       print_r($data);
       echo "</pre>";*/
        $credit = 0;
        $debit = 0;

        foreach ($data as $row) {
            if ($row['idoperation'] == "1") {
                $debit += $row['montant'];
            }
            if ($row['idoperation'] == "2") {
                $credit += $row['montant'];
            }
        }
        //print_r($credit);
        //$dif_old=0;
        //for ($i=1;$i<=12;$i++)
        //{
        //	$dif=$dif_old+$credit[$i]-$debit[$i];
        $tableau = [
            "debit" => $debit,
            "credit" => $credit,
            "dif" => $credit - $debit,
        ];
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

    public function obtenirJournal(string $debitCredit = '',
                            $periode_debut = '',
                            $periode_fin = '',
                            $onlyUnclasifedEntries = true,
    ) {
        $periode_debut = $this->periodeDebutFin($debutFin = 'debut', $periode_debut);
        $periode_fin = $this->periodeDebutFin($debutFin = 'fin', $periode_fin);

        $filtre = $debitCredit == 1 || $debitCredit == 2 ? 'AND compta.idoperation =\'' . $debitCredit . '\'  ' : "";

        $requete = 'SELECT ';
        $requete .= 'compta.date_ecriture, compta.description, compta.montant, compta.idoperation,compta.id as idtmp, ';
        $requete .= 'compta.comment, compta.attachment_required, compta.attachment_filename, ';
        $requete .= 'compta_reglement.reglement, ';
        $requete .= 'compta_evenement.evenement, ';
        $requete .= 'compta_categorie.categorie, ';
        $requete .= 'compta_compte.nom_compte,    ';
        $requete .= '(COALESCE(compta.montant_ht_soumis_tva_0,0) + COALESCE(compta.montant_ht_soumis_tva_5_5,0) + COALESCE(compta.montant_ht_soumis_tva_10, 0) + COALESCE(compta.montant_ht_soumis_tva_20, 0)) as montant_ht,   ';
        $requete .= '((COALESCE(compta.montant_ht_soumis_tva_5_5, 0)*0.055) + (COALESCE(compta.montant_ht_soumis_tva_10, 0)*0.1) + (COALESCE(compta.montant_ht_soumis_tva_20, 0)*0.2)) as montant_tva,   ';
        $requete .= 'compta.montant_ht_soumis_tva_0 as montant_ht_0,   ';
        $requete .= 'compta.montant_ht_soumis_tva_5_5 as montant_ht_5_5,   ';
        $requete .= 'compta.montant_ht_soumis_tva_5_5*0.055 as montant_tva_5_5,   ';
        $requete .= 'compta.montant_ht_soumis_tva_10 as montant_ht_10,   ';
        $requete .= 'compta.montant_ht_soumis_tva_10*0.1 as montant_tva_10,   ';
        $requete .= 'compta.montant_ht_soumis_tva_20 as montant_ht_20,   ';
        $requete .= 'compta.montant_ht_soumis_tva_20*0.2 as montant_tva_20,   ';
        $requete .= 'compta.tva_zone   ';
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
    public function formatMontantCompta($valeur): string
    {
        return number_format($valeur, 2, ',', ' ');
    }

    public function periodeDebutFin($debutFin = 'debut', $date = '')
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
            return date("Y") . "-01-01";
        //		}
        } else {
            /*	if ($id_periode !='')
                {
                     $r=obtenirPeriodeEnCours($id_periode);
                     print_r($r);
                     return $r;
                } else {*/
            return date("Y") . "-12-31";
            //}
        }
    }

    public function obtenirPeriodeEnCours($id_periode)
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
        } else {                // ajout d'une nouvelle periode
            $result = $this->ajouterListPeriode();
            return $result[0]['id'];
        }
    }

    public function ajouterListPeriode()
    {
        $date_debut = date("Y") . '-01-01';
        $date_fin = date("Y") . '-12-31';

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

    public function obtenirListPeriode(?string $date_debut = '', ?string $date_fin = '')
    {
        $requete = 'SELECT ';
        $requete .= 'id, date_debut,date_fin, verouiller ';
        $requete .= 'FROM  ';
        $requete .= 'compta_periode  ';

        if ($date_debut != '' && $date_fin != '') {
            $requete .= 'WHERE ';
            $requete .= 'compta_periode.date_debut= \'' . $date_debut . '\'  ';
            $requete .= 'AND compta_periode.date_fin= \'' . $date_fin . '\'  ';
        }

        return $this->_bdd->obtenirTous($requete);
    }

    public function obtenirListOperations($filtre = '', ?string $where = '')
    {
        $requete = 'SELECT ';
        $requete .= 'id, operation ';
        $requete .= 'FROM  ';
        $requete .= 'compta_operation  ';
        if ($where) {
            $requete .= 'WHERE id=' . $where . ' ';
        }

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

    public function obtenirListComptes($filtre = '', ?string $where = '')
    {
        $requete = 'SELECT ';
        $requete .= 'id, nom_compte ';
        $requete .= 'FROM  ';
        $requete .= 'compta_compte  ';
        if ($where) {
            $requete .= 'WHERE id=' . $where . ' ';
        }

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

    public function obtenirListCategories($filtre = '', ?string $where = '', $usedInAccountingJournal = false)
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

        if ($wheres !== []) {
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

    public function obtenirListEvenements($filtre = '', ?string $where = '', $usedInAccountingJournal = false)
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

        if ($wheres !== []) {
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

    public function obtenirListReglements($filtre = '', ?string $where = '', $usedInAccountingJournal = false)
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

        if ($wheres !== []) {
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

    public function ajouter($idoperation, $idcompte, $idcategorie, $date_ecriture, $nom_frs, $tva_intra, $montant, $description,
                     $numero, $idmode_regl, $date_regl, $obs_regl, $idevenement, $numero_operation = null,
                     $attachmentRequired = 0, $montantHtSoumisTva0 = null, $montantHtSoumisTva55 = null, $montantHtSoumisTva10 = null, $montantHtSoumisTva20 = null, $tvaZone = null)
    {
        $requete = 'INSERT INTO ';
        $requete .= 'compta (';
        $requete .= 'idoperation,idcategorie,date_ecriture,nom_frs,tva_intra,montant,description,';
        $requete .= 'numero,idmode_regl,date_regl,obs_regl,idevenement, numero_operation,idcompte, attachment_required,
        montant_ht_soumis_tva_0, montant_ht_soumis_tva_5_5, montant_ht_soumis_tva_10, montant_ht_soumis_tva_20, tva_zone
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
        $requete .= ($montantHtSoumisTva0 ? $this->_bdd->echapper($montantHtSoumisTva0) : 'NULL') . ',';
        $requete .= ($montantHtSoumisTva55 ? $this->_bdd->echapper($montantHtSoumisTva55) : 'NULL') . ',';
        $requete .= ($montantHtSoumisTva10 ? $this->_bdd->echapper($montantHtSoumisTva10) : 'NULL') . ',';
        $requete .= ($montantHtSoumisTva20 ? $this->_bdd->echapper($montantHtSoumisTva20) : 'NULL') . ',';
        $requete .= ($tvaZone ? $this->_bdd->echapper($tvaZone) : 'NULL') . '';
        $requete .= ');';

        $resultat = $this->_bdd->executer($requete);
        if ($resultat) {
            $this->lastId = $this->_bdd->obtenirDernierId();
        }
        return $resultat;
    }

    public function modifier(string $id, $idoperation, $idcompte, $idcategorie, $date_ecriture, $nom_frs, $tva_intra, $montant, $description,
                      $numero, $idmode_regl, $date_regl, $obs_regl, $idevenement, $comment, $numero_operation = null, $attachmentRequired = 0,
                      $montantHtSoumisTva0 = null, $montantHtSoumisTva55 = null, $montantHtSoumisTva10 = null, $montantHtSoumisTva20 = null,
                      $tvaZone = null,
    ) {
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
        $requete .= 'montant_ht_soumis_tva_0=' . ($montantHtSoumisTva0 ? $this->_bdd->echapper($montantHtSoumisTva0) : 'NULL') . ',';
        $requete .= 'montant_ht_soumis_tva_5_5=' . ($montantHtSoumisTva55 ? $this->_bdd->echapper($montantHtSoumisTva55) : 'NULL') . ',';
        $requete .= 'montant_ht_soumis_tva_10=' . ($montantHtSoumisTva10 ? $this->_bdd->echapper($montantHtSoumisTva10) : 'NULL') . ',';
        $requete .= 'montant_ht_soumis_tva_20=' . ($montantHtSoumisTva20 ? $this->_bdd->echapper($montantHtSoumisTva20) : 'NULL') . ',';
        $requete .= 'tva_zone=' . ($tvaZone ? $this->_bdd->echapper($tvaZone) : 'NULL') . ',';
        $requete .= 'comment=' . ($comment ? $this->_bdd->echapper($comment) : 'NULL') . ',';
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

    public function ajouterConfig(string $table, string $champ, $valeur)
    {
        $requete = 'INSERT INTO ';
        $requete .= '' . $table . ' (';
        $requete .= '' . $champ . ') ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($valeur) . ' ';
        $requete .= ');';

        return $this->_bdd->executer($requete);
    }

    public function modifierConfig(string $table, string $id, string $champ, $valeur)
    {
        $requete = 'UPDATE ';
        $requete .= '' . $table . ' ';
        $requete .= 'SET ';
        $requete .= '' . $champ . ' = ' . $this->_bdd->echapper($valeur) . ' ';
        $requete .= 'WHERE ';
        $requete .= 'id = ' . $id . ' ';

        return $this->_bdd->executer($requete);
    }

    public function obtenir(int $id)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';
        $requete .= 'WHERE id=' . $id;

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public function supprimerEcriture(string $id)
    {
        $requete = 'DELETE FROM compta WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }

    public function obtenirParNumeroOperation($numero_operation)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';
        $requete .= 'WHERE numero_operation=' . $this->_bdd->echapper($numero_operation);

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public function obtenirSuivantADeterminer($numero_operation)
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

    public function obtenirTous()
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';

        return $this->_bdd->obtenirTous($requete);
    }

    public function obtenirEvenementParIdForum($id)
    {
        $requete = 'SELECT ';
        $requete .= '  compta_evenement.id ';
        $requete .= 'FROM ';
        $requete .= '  compta_evenement ';
        $requete .= 'INNER JOIN ';
        $requete .= '  afup_forum on afup_forum.titre = compta_evenement.evenement ';
        $requete .= 'WHERE ';
        $requete .= '  afup_forum.id = ' . (int) $id;
        return $this->_bdd->obtenirUn($requete);
    }

    public function extraireComptaDepuisCSVBanque(Importer $importer): bool
    {
        if (!$importer->validate()) {
            return false;
        }

        $qualifier = new AutoQualifier($this->obtenirListRegles(true));

        foreach ($importer->extract() as $operation) {
            $numero_operation = $operation->getNumeroOperation();
            // On vérife si l'enregistrement existe déjà
            $enregistrement = $this->obtenirParNumeroOperation($numero_operation);

            $operationQualified = $qualifier->qualify($operation);

            if (!is_array($enregistrement)) {
                $this->ajouter(
                    $operationQualified['idoperation'],
                    $importer->getCompteId(),
                    $operationQualified['categorie'],
                    $operationQualified['date_ecriture'],
                    '',
                    '',
                    $operationQualified['montant'],
                    $operationQualified['description'],
                    '',
                    $operationQualified['idModeReglement'],
                    $operationQualified['date_ecriture'],
                    '',
                    $operationQualified['evenement'],
                    $operationQualified['numero_operation'],
                    $operationQualified['attachmentRequired'],
                    $operationQualified['montant_ht_soumis_tva_0'],
                    $operationQualified['montant_ht_soumis_tva_5_5'],
                    $operationQualified['montant_ht_soumis_tva_10'],
                    $operationQualified['montant_ht_soumis_tva_20']
                );
            } else {
                $modifier = false;
                if ($enregistrement['idcategorie'] == AutoQualifier::DEFAULT_CATEGORIE && $operationQualified['categorie'] != AutoQualifier::DEFAULT_CATEGORIE) {
                    $enregistrement['idcategorie'] = $operationQualified['categorie'];
                    $modifier = true;
                }
                if ($enregistrement['idevenement'] == AutoQualifier::DEFAULT_EVENEMENT && $operationQualified['evenement'] != AutoQualifier::DEFAULT_EVENEMENT) {
                    $enregistrement['idevenement'] = $operationQualified['evenement'];
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
                        $operationQualified['attachmentRequired']
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
    public function rechercher($query): array
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

    public function obtenirListRegles($filtre = '', $where = '')
    {
        $requete = 'SELECT ';
        $requete .= '`id`, `label`, `condition`, `is_credit`, `vat`, `category_id`, `event_id`, `mode_regl_id`, `attachment_required` ';
        $requete .= 'FROM ';
        $requete .= 'compta_regle ';
        $wheres = [];
        if ($where) {
            $wheres[] = 'id=' . intval($where) . ' ';
        }

        if ($wheres !== []) {
            $requete .= sprintf('WHERE %s ',implode(' AND ', $wheres));
        }

        $requete .= 'ORDER BY ';
        $requete .= 'label ';

        if ($where) {
            return $this->_bdd->obtenirEnregistrement($requete);
        } elseif ($filtre) {
            return $this->_bdd->obtenirTous($requete);
        }
        return null;
    }

    public function ajouterRegle($label, $condition, $is_credit, $tva, $category_id, $event_id, $mode_regl_id, $attachment_required)
    {
        $requete = 'INSERT INTO ';
        $requete .= 'compta_regle (';
        $requete .= '`label`, `condition`, `is_credit`, `vat`, `category_id`, `event_id`, `mode_regl_id`, `attachment_required`) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($label) . ', ';
        $requete .= $this->_bdd->echapper($condition) . ', ';
        $requete .= $this->_bdd->echapper($is_credit) . ', ';
        $requete .= $this->_bdd->echapper($tva) . ', ';
        $requete .= $this->_bdd->echapper($category_id) . ', ';
        $requete .= $this->_bdd->echapper($event_id) . ', ';
        $requete .= $this->_bdd->echapper($mode_regl_id) . ', ';
        $requete .= $this->_bdd->echapper($attachment_required) . ' ';
        $requete .= ');';

        return $this->_bdd->executer($requete);
    }

    public function modifierRegle($id, $label, $condition, $is_credit, $tva, $category_id, $event_id, $mode_regl_id, $attachment_required)
    {
        $requete = 'UPDATE ';
        $requete .= 'compta_regle ';
        $requete .= 'SET ';
        $requete .= '`label` = ' . $this->_bdd->echapper($label) . ', ';
        $requete .= '`condition` = ' . $this->_bdd->echapper($condition) . ', ';
        $requete .= '`is_credit` = ' . $this->_bdd->echapper($is_credit) . ', ';
        $requete .= '`vat` = ' . $this->_bdd->echapper($tva) . ', ';
        $requete .= '`category_id` = ' . $this->_bdd->echapper($category_id) . ', ';
        $requete .= '`event_id` = ' . $this->_bdd->echapper($event_id) . ', ';
        $requete .= '`mode_regl_id` = ' . $this->_bdd->echapper($mode_regl_id) . ', ';
        $requete .= '`attachment_required` = ' . $this->_bdd->echapper($attachment_required) . ' ';
        $requete .= 'WHERE ';
        $requete .= 'id = ' . intval($id) . ' ';

        return $this->_bdd->executer($requete);
    }

    public static function getTvaZoneLabel($tvaZoneCode, $defaultValue = null)
    {
        if (!isset(self::TVA_ZONES[$tvaZoneCode])) {
            return $defaultValue;
        }

        return self::TVA_ZONES[$tvaZoneCode];
    }
}
