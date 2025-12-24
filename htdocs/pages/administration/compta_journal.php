<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction([
    'lister',
    'debit',
    'credit',
    'ajouter',
    'modifier',
    'modifier_colonne',
    'upload_attachment',
]);

$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);


$id_periode = isset($_GET['id_periode']) && $_GET['id_periode'] ? $_GET['id_periode'] : "";

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode);


$periode_debut = $listPeriode[$id_periode - 1]['date_debut'];
$periode_fin = $listPeriode[$id_periode - 1]['date_fin'];

if (in_array($action, ['lister', 'debit', 'credit'])) {
    $alsoDisplayClassifed = isset($_GET['also_display_classifed_entries']) && $_GET['also_display_classifed_entries'];

    $smarty->assign('also_display_classifed_entries', $alsoDisplayClassifed);
}

if (in_array($action, ['lister', 'debit', 'credit'])) {
    $smarty->assign('categories', $compta->obtenirListCategoriesJournal());
    $smarty->assign('events', $compta->obtenirListEvenementsJournal());
    $smarty->assign('payment_methods', $compta->obtenirListReglementsJournal());
}

if ($action == 'lister') {
    // Accounting lines for the selected period
    $journal = $compta->obtenirJournal('', $periode_debut, $periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
} elseif ($action == 'debit') {
    $journal = $compta->obtenirJournal('1',$periode_debut,$periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
} elseif ($action == 'credit') {
    $journal = $compta->obtenirJournal('2',$periode_debut,$periode_fin, !$alsoDisplayClassifed);
    $smarty->assign('journal', $journal);
} elseif ($action == 'ajouter' || $action == 'modifier') {
    $formulaire = instancierFormulaire();

    if ($action === 'modifier') {
        $champsRecup = $compta->obtenir((int) $_GET['id']);

        $champs['idcompte']          = $champsRecup['idcompte'];
        $champs['date_saisie']          = $champsRecup['date_ecriture'];
        $champs['idoperation']          = $champsRecup['idoperation'];
        $champs['idcategorie']          = $champsRecup['idcategorie'];
        $champs['nom_frs']          = $champsRecup['nom_frs'];
        $champs['tva_intra']        = $champsRecup['tva_intra'];
        $champs['montant']          = $champsRecup['montant'];
        $champs['description']          = $champsRecup['description'];
        $champs['numero']          = $champsRecup['numero'];
        $champs['idmode_regl']          = $champsRecup['idmode_regl'];
        $champs['date_reglement']          = $champsRecup['date_regl'];
        $champs['obs_regl']          = $champsRecup['obs_regl'];
        $champs['idevenement']          = $champsRecup['idevenement'];
        $champs['comment'] = $champsRecup['comment'];
        $champs['montant_ht_soumis_tva_0'] = $champsRecup['montant_ht_soumis_tva_0'];
        $champs['montant_ht_soumis_tva_5_5'] = $champsRecup['montant_ht_soumis_tva_5_5'];
        $champs['montant_ht_soumis_tva_10'] = $champsRecup['montant_ht_soumis_tva_10'];
        $champs['montant_ht_soumis_tva_20'] = $champsRecup['montant_ht_soumis_tva_20'];
        $champs['tva_zone'] = $champsRecup['tva_zone'];



        //$formulaire->setDefaults($champsRecup);
        $formulaire->addElement('hidden', 'id', $_GET['id']);
    } else {
        $champs['idcompte'] = 1;
        $champs['date_saisie'] = date('Y-m-d');
        $champs['date_reglement'] = date('Y-m-d');
    }
    $formulaire->setDefaults($champs);

    // facture associé à un évènement
    $formulaire->addElement('header'  , ''                         , 'Sélectionner un Journal');
    $formulaire->addElement('select'  , 'idoperation', 'Type d\'opération', $compta->obtenirListOperations());
    $formulaire->addElement('select'  , 'idcompte'   , 'Compte', $compta->obtenirListComptes());
    $formulaire->addElement('select'  , 'idevenement', 'Evenement', $compta->obtenirListEvenements());

    //detail facture
    $formulaire->addElement('header'  , ''                         , 'Détail Facture');

    //$mois=10;
    $formulaire->addElement('date'    , 'date_saisie'     , 'Date saisie', ['language' => 'fr',
        'format'   => 'd F Y',
        'minYear' => date('Y') - 5,
        'maxYear' => date('Y') + 1]);

    $formulaire->addElement('select'  , 'idcategorie', 'Type de compte', $compta->obtenirListCategories());
    $formulaire->addElement('text', 'nom_frs', 'Nom fournisseurs' , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'    , 'tva_intra'  , 'TVA intracommunautaire (facture)', ['size' => 30, 'maxlength' => 100]);
    $formulaire->addElement('text', 'numero', 'Numero facture' , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('textarea', 'description', 'Description', ['cols' => 42, 'rows' => 5]);
    $formulaire->addElement('text', 'montant', 'Montant' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_montant']);
    $formulaire->addElement('text', 'comment', 'Commentaire' , ['size' => 30, 'maxlength' => 255]);

    $formulaire->addElement('header'  , ''                         , 'TVA');
    $formulaire->addElement('text', 'montant_ht_soumis_tva_5_5', 'Montant HT soumis à TVA 5.5%' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_ht_5_5']);
    $formulaire->addElement('static'  , 'note', '', '<a href="#" id="apply-vat-5-5">Calculer le montant HT soumis à TVA 5.5% sur la base de l\'intégralité du montant TTC</a><br /><br />');
    $formulaire->addElement('text', 'montant_ht_soumis_tva_10', 'Montant HT soumis à TVA 10%' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_ht_10']);
    $formulaire->addElement('static'  , 'note', '', '<a href="#" id="apply-vat-10">Calculer le montant HT soumis à TVA 10% sur la base de l\'intégralité du montant TTC</a><br /><br />');
    $formulaire->addElement('text', 'montant_ht_soumis_tva_20', 'Montant HT soumis à TVA 20%' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_ht_20']);
    $formulaire->addElement('static'  , 'note', '', '<a href="#" id="apply-vat-20">Calculer le montant HT soumis à TVA 20% sur la base de l\'intégralité du montant TTC</a><br /><br />');
    $formulaire->addElement('text', 'montant_ht_soumis_tva_0', 'Montant HT non soumis à TVA' , ['size' => 30, 'maxlength' => 40, 'id' => 'compta_journal_ht_0']);
    $formulaire->addElement('static'  , 'note', '', '<a href="#" id="apply-vat-0">Calculer le montant non soumis à TVA sur la base de l\'intégralité du montant TTC</a><br /><br />');

    $formulaire->addElement('select'  , 'tva_zone', 'Zone TVA', array_merge(['' => 'Non définie'], Comptabilite::TVA_ZONES));

    //reglement
    $formulaire->addElement('header'  , ''                         , 'Réglement');
    $formulaire->addElement('select'  , 'idmode_regl', 'Réglement', $compta->obtenirListReglements());
    $formulaire->addElement('date'    , 'date_reglement'     , 'Date', ['language' => 'fr',
        'format'   => 'd F Y',
        'minYear' => date('Y') - 5,
        'maxYear' => date('Y') + 1]);
    $formulaire->addElement('text', 'obs_regl', 'Info reglement' , ['size' => 30, 'maxlength' => 40]);


    // boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

    // 2012-02-18 A. Gendre
    $passer = null;
    if ($action !== 'ajouter') {
        $res = $compta->obtenirSuivantADeterminer($_GET['id']);
        if (is_array($res)) {
            $passer = $res['id'];
            $formulaire->addElement('submit', 'soumettrepasser'   , 'Soumettre & passer');
            $formulaire->addElement('submit', 'passer'   , 'Passer');
        }
    }

    // ajoute des regles
    $formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'required');
    $formulaire->addRule('idcompte'      , 'Compte manquant'    , 'required');
    $formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'nonzero');
    $formulaire->addRule('idevenement'    , 'Evenement manquant'   , 'required');
    $formulaire->addRule('idevenement'    , 'Evenement manquant'   , 'nonzero');
    $formulaire->addRule('idcategorie'    , 'Type de compte manquant'     , 'required');
    $formulaire->addRule('idcategorie'    , 'Type de compte manquant'     , 'nonzero');
    $formulaire->addRule('montant'       , 'Montant manquant'      , 'required');


    // 2012-02-18 A. Gendre
    if (isset($_POST['passer']) && isset($passer)) {
        afficherMessage('L\'écriture n\'a pas été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=compta_journal&action=modifier&id=' . $passer);
        return;
    }

    if ($formulaire->validate()) {
        $valeur = $formulaire->exportValues();

        $date_ecriture = $valeur['date_saisie']['Y'] . "-" . $valeur['date_saisie']['F'] . "-" . $valeur['date_saisie']['d'] ;
        $date_regl = $valeur['date_reglement']['Y'] . "-" . $valeur['date_reglement']['F'] . "-" . $valeur['date_reglement']['d'] ;

        if ($action === 'ajouter') {
            $ok = $compta->ajouter(
                                    $valeur['idoperation'],
                                    $valeur['idcompte'],
                                    $valeur['idcategorie'],
                                    $date_ecriture,
                                    $valeur['nom_frs'],
                                    $valeur['tva_intra'],
                                    $valeur['montant'],
                                    $valeur['description'],
                                    $valeur['numero'],
                                    $valeur['idmode_regl'],
                                    $date_regl,
                                    $valeur['obs_regl'],
                                    $valeur['idevenement'],
                                    $valeur['comment'],
                                    0,
                                    $valeur['montant_ht_soumis_tva_0'],
                                    $valeur['montant_ht_soumis_tva_5_5'],
                                    $valeur['montant_ht_soumis_tva_10'],
                                    $valeur['montant_ht_soumis_tva_20'],
                                    $valeur['tva_zone'],

                                    );
        } else {
            $ok = $compta->modifier(
                                    $valeur['id'],
                                    $valeur['idoperation'],
                                    $valeur['idcompte'],
                                    $valeur['idcategorie'],
                                    $date_ecriture,
                                    $valeur['nom_frs'],
                                    $valeur['tva_intra'],
                                    $valeur['montant'],
                                    $valeur['description'],
                                    $valeur['numero'],
                                    $valeur['idmode_regl'],
                                    $date_regl,
                                    $valeur['obs_regl'],
                                    $valeur['idevenement'],
                                    $valeur['comment'],
                                    null,
                                    0,
                                    $valeur['montant_ht_soumis_tva_0'],
                                    $valeur['montant_ht_soumis_tva_5_5'],
                                    $valeur['montant_ht_soumis_tva_10'],
                                    $valeur['montant_ht_soumis_tva_20'],
                                    $valeur['tva_zone'],
                                    );
        }
        if ($ok) {
            if ($action === 'ajouter') {
                Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            // 2012-02-18 A. Gendre
            if (isset($_POST['soumettrepasser']) && isset($passer)) {
                $urlredirect = 'index.php?page=compta_journal&action=modifier&id=' . $passer;
            } else {
                $urlredirect = 'index.php?page=compta_journal&action=lister#L' . $valeur['id'];
            }
            afficherMessage('L\'écriture a été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'), $urlredirect);
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action === 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }


    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
