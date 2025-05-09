<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;
use Afup\Site\Comptabilite\Facture;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\Utils;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction([
                    'lister',
                    'modifier',
                    'telecharger_facture',
                    'envoyer_facture',
                    ]);


//$action = verifierAction(array('lister', 'devis','facture','ajouter', 'modifier'));
//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);
$comptaFact = new Facture($bdd);

if ($action == 'lister') {
    $periodes = $compta->obtenirListPeriode();
    $id_periode = isset($_GET['id_periode']) && $_GET['id_periode'] ? $_GET['id_periode'] : end($periodes)['id'];
    $ecritures = $comptaFact->obtenirFacture($id_periode);
    foreach ($ecritures as &$e) {
        $e['link'] = urlencode(Utils::cryptFromText($e['id']));
    }
    $smarty->assign('id_periode', $id_periode);
    $smarty->assign('ecritures', $ecritures);
    $smarty->assign('listPeriode', $periodes);
} elseif ($action == 'telecharger_facture') {
    $comptaFact->genererFacture($_GET['ref']);
} elseif ($action == 'envoyer_facture') {
    if ($comptaFact->envoyerfacture($_GET['ref'])) {
        Logs::log('Envoi par email de la facture n°' . $_GET['ref']);
        afficherMessage('La facture a été envoyée', 'index.php?page=compta_facture&action=lister');
    } else {
        afficherMessage("La facture n'a pas pu être envoyée", 'index.php?page=compta_facture&action=lister', true);
    }
} elseif ($action == 'envoyer_facture') {
    if ($comptaFact->envoyerFacture($_GET['ref'])) {
        Logs::log('Envoi par email de la facture n°' . $_GET['ref']);
        afficherMessage('La facture a été envoyée', 'index.php?page=compta_facture&action=lister');
    } else {
        afficherMessage("La facture n'a pas pu être envoyée", 'index.php?page=compta_facture&action=lister', true);
    }
} elseif ($action == 'ajouter' || $action == 'modifier') {
    $pays = new Pays($bdd);

    $formulaire = instancierFormulaire();

    if ($action === 'modifier') {
        $champsRecup = $comptaFact->obtenir($_GET['id']);

        $factureId = $champsRecup['id'];

        $champs['date_facture']          = $champsRecup['date_facture'];
        $champs['societe']          = $champsRecup['societe'];
        $champs['service']          = $champsRecup['service'];
        $champs['adresse']          = $champsRecup['adresse'];
        $champs['code_postal']          = $champsRecup['code_postal'];
        $champs['ville']          = $champsRecup['ville'];
        $champs['id_pays']          = $champsRecup['id_pays'];
        $champs['email']          = $champsRecup['email'];
        $champs['tva_intra']          = $champsRecup['tva_intra'];
        $champs['observation']          = $champsRecup['observation'];
        $champs['ref_clt1']          = $champsRecup['ref_clt1'];
        $champs['ref_clt2']          = $champsRecup['ref_clt2'];
        $champs['ref_clt3']          = $champsRecup['ref_clt3'];
        $champs['nom']          = $champsRecup['nom'];
        $champs['prenom']          = $champsRecup['prenom'];
        $champs['tel']          = $champsRecup['tel'];
        $champs['numero_devis']          = $champsRecup['numero_devis'];
        $champs['numero_facture']          = $champsRecup['numero_facture'];
        $champs['etat_paiement']          = $champsRecup['etat_paiement'];
        $champs['date_paiement']          = $champsRecup['date_paiement'];
        $champs['devise_facture']          = $champsRecup['devise_facture'];


        $champsRecup = $comptaFact->obtenir_details($_GET['id']);

        $i=1;
        foreach ($champsRecup as $row) {
            $champs['id' . $i]          = $row['id'];
            $champs['ref' . $i]          = $row['ref'];
            $champs['designation' . $i]          = $row['designation'];
            $champs['quantite' . $i]          = $row['quantite'];
            $champs['pu' . $i]          = $row['pu'];
            $champs['tva' . $i]          = $row['tva'];
            $i++;
        }



        $formulaire->setDefaults($champs);
        //$formulaire->setDefaults($champsRecup);
        $formulaire->addElement('hidden', 'id', $_GET['id']);

        $smarty->assign('facture_id', $factureId);
    }

    //detail devis
    $formulaire->addElement('header'  , ''                         , 'Détail Devis');

    //$mois=10;
    if ($action === 'modifier') {
        $formulaire->addElement('date'    , 'date_facture'     , 'Date facture', ['language' => 'fr',
                                                                                'format'   => 'd F Y',
                                                                                'minYear' => date('Y')-3,
                                                                                'maxYear' => date('Y')]);
    } else {
        $formulaire->addElement('date'    , 'date_facture'     , 'Date facture', ['language' => 'fr',
                                                                                'format'   => 'd F Y',
                                                                                'minYear' => date('Y'),
                                                                                'maxYear' => date('Y')]);
    }
    $formulaire->addElement('header'  , ''                       , 'Facturation');
    $formulaire->addElement('static'  , 'note'                   , ''               , 'Ces informations concernent la personne ou la société qui sera facturée<br /><br />');
    $formulaire->addElement('text'    , 'societe'    , 'Société'        , ['size' => 50, 'maxlength' => 100]);
    $formulaire->addElement('text'    , 'service'        , 'Service'            , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('textarea', 'adresse'    , 'Adresse'        , ['cols' => 42, 'rows'      => 10]);
    $formulaire->addElement('text'    , 'code_postal', 'Code postal'    , ['size' =>  6, 'maxlength' => 10]);
    $formulaire->addElement('text'    , 'ville'      , 'Ville'          , ['size' => 30, 'maxlength' => 50]);
    $formulaire->addElement('select'  , 'id_pays'    , 'Pays'           , $pays->obtenirPays());

    $formulaire->addElement('header', null          , 'Contact');
    $formulaire->addElement('text'    , 'nom'        , 'Nom'            , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'    , 'prenom'     , 'Prénom'         , ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'    , 'tel'        , 'Numero de tél'	, ['size' => 30, 'maxlength' => 40]);
    $formulaire->addElement('text'    , 'email'      , 'Email (facture)', ['size' => 30, 'maxlength' => 100]);
    $formulaire->addElement('text'    , 'tva_intra'  , 'TVA intracommunautaire (facture)', ['size' => 30, 'maxlength' => 100]);

    if ($champs['numero_devis'] || $champs['numero_facture']) {
        $formulaire->addElement('header', null          , 'Réservé à l\'administration');
        $formulaire->addElement('static'  , 'note'                   , ''               , 'Numéro généré automatiquement et affiché en automatique');
        if ($champs['numero_devis']) {
            $formulaire->addElement('text'  , 'numero_devis'   , 'Numéro devis'   , ['size' => 50, 'maxlength' => 100]);
        }
        if ($champs['numero_facture']) {
            $formulaire->addElement('text'  , 'numero_facture'   , 'Numéro facture'   , ['size' => 50, 'maxlength' => 100]);
        }
    } else {
        $formulaire->addElement('hidden'  , 'numero_devis'   , 'Numéro devis'   , ['size' => 50, 'maxlength' => 100]);
        $formulaire->addElement('hidden'  , 'numero_facture'   , 'Numéro facture'   , ['size' => 50, 'maxlength' => 100]);
    }

    $formulaire->addElement('header', null          , 'Référence client');
    $formulaire->addElement('static'  , 'note'  , '', 'Possible d\'avoir plusieurs références à mettre (obligation client)<br /><br />');
    $formulaire->addElement('text'  , 'ref_clt1'   , 'Référence client'   , ['size' => 50, 'maxlength' => 100]);
    $formulaire->addElement('text'  , 'ref_clt2' , 'Référence client 2', ['size' => 50, 'maxlength' => 100]);
    $formulaire->addElement('text'  , 'ref_clt3' , 'Référence client 3' , ['size' => 50, 'maxlength' => 100]);



    $formulaire->addElement('header'  , '', 'Observation');
    $formulaire->addElement('static'  , 'note'     , ''  , 'Ces informations seront écrites à la fin du document<br /><br />');
    $formulaire->addElement('textarea', 'observation'  , 'Observation', ['cols' => 42, 'rows' => 5]);

    $formulaire->addElement('header'  , '', 'Paiement');
    $formulaire->addElement('select', 'devise_facture'  , 'Monnaie de la facture', ['EUR' => 'Euro',
                                                                                        'DOL' => 'Dollar'], ['size' => 2]);
    $formulaire->addElement('select', 'etat_paiement'  , 'Etat paiement', ['En attente de paiement', 'Payé', 'Annulé'], ['size' => 3]);
    $formulaire->addElement('date'    , 'date_paiement'     , 'Date paiement', ['language' => 'fr', 'format'   => 'd F Y', 'minYear' => date('Y') - 5, 'maxYear' => date('Y')]);



    $formulaire->addElement('header'  , '', 'Contenu');
    $formulaire->addElement('text'    , 'ref'    , 'Référence'        , ['size' => 50, 'maxlength' => 100]);
    $formulaire->addElement('textarea', 'designation'  , 'Désignation', ['cols' => 42, 'rows' => 5]);
    $formulaire->addElement('text'    , 'quantite'    , 'Quantite'        , ['size' => 50, 'maxlength' => 100]);
    $formulaire->addElement('text'    , 'pu'    , 'Prix Unitaire'        , ['size' => 50, 'maxlength' => 100]);



    for ($i=1;$i<6;$i++) {
        $formulaire->addElement('header'  , '', 'Contenu');
        $formulaire->addElement('static'  , 'note'     , ''  , 'Ligne ' . $i . '<br /><br />');
        $formulaire->addElement('hidden'    , 'id' . $i    , 'id');
        $formulaire->addElement('text'    , 'ref' . $i    , 'Référence'        , ['size' => 50, 'maxlength' => 100]);
        $formulaire->addElement('static'  , 'note'     , ''  , 'Rappel : sponsoring 20%, place supplémentaire 10%.<br />');
        $formulaire->addElement('select'    , 'tva' . $i    , 'Taux de TVA'        , ['0' => 'Non soumis', '5.50' => '5.5%', '10.00' => '10%', '20.00' => '20%']);
        $formulaire->addElement('textarea', 'designation' . $i  , 'Désignation', ['cols' => 42, 'rows' => 5]);
        $formulaire->addElement('text'    , 'quantite' . $i    , 'Quantite'        , ['size' => 50, 'maxlength' => 100]);
        $formulaire->addElement('text'    , 'pu' . $i    , 'Prix Unitaire HT'        , ['size' => 50, 'maxlength' => 100]);
    }





    // boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

    // ajoute des regles
    //	$formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'required');
    //	$formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'nonzero');
    $formulaire->addRule('societe'       , 'Société manquant'      , 'required');
    $formulaire->addRule('adresse'       , 'Adresse manquant'      , 'required');
    $formulaire->addRule('email'       , 'Email manquant'      , 'required');

    if ($formulaire->validate()) {
        $valeur = $formulaire->exportValues();

        $date_ecriture= $valeur['date_facture']['Y'] . "-" . $valeur['date_facture']['F'] . "-" . $valeur['date_facture']['d'] ;
        $date_paiement= $valeur['date_paiement']['Y'] . "-" . $valeur['date_paiement']['F'] . "-" . $valeur['date_paiement']['d'] ;
        if ($action === 'ajouter') {
            // il faut passser obligatoirement par un devis
        } else {
            $ok = $comptaFact->modifier(
                $_GET['id'],
                $date_ecriture,
                $valeur['societe'],
                $valeur['service'],
                $valeur['adresse'],
                $valeur['code_postal'],
                $valeur['ville'],
                $valeur['id_pays'],
                $valeur['nom'],
                $valeur['prenom'],
                $valeur['tel'],
                $valeur['email'],
                $valeur['tva_intra'],
                $valeur['observation'],
                $valeur['ref_clt1'],
                $valeur['ref_clt2'],
                $valeur['ref_clt3'],
                $valeur['numero_devis'],
                $valeur['numero_facture'],
                $valeur['etat_paiement'],
                $date_paiement,
                $valeur['devise_facture']
            );

            for ($i=1;$i<6;$i++) {
                $ok = $comptaFact->modifier_details(
                    $valeur['id' . $i],
                    $valeur['ref' . $i],
                    $valeur['designation' . $i],
                    (int) $valeur['quantite' . $i],
                    (float) $valeur['pu' . $i],
                    (int) $valeur['tva' . $i]
                );
            }
        }

        if ($ok) {
            if ($action === 'ajouter') {
                Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('L\'écriture a été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=compta_facture&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action === 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }


    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
