<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'preparer', 'envoyer','listing'));
$tris_valides = array('nom', 'date_consultation', 'presence', 'personnes_avec_pouvoir_nom');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Assemblee_Generale.php';
$assemblee_generale = new AFUP_Assemblee_Generale($bdd);

if ($action == 'lister' || $action== 'listing' ) {

    // Valeurs par défaut des paramètres de tri
    $timestamp = $assemblee_generale->obternirDerniereDate();
    $list_date_assemblee_generale = convertirTimestampEnDate($timestamp);
    $list_ordre = 'nom';
    $list_sens = 'asc';
    $list_associatif = false;

    // Modification des paramètres de tri en fonction des demandes passées en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['date'])) {
	    	$list_date_assemblee_generale = $_GET['date'];
    } else {
	    	$_GET['date'] = $list_date_assemblee_generale;
    }

    if ($action == "listing")
    {
    	$list_ordre="nom";
    }

    // Mise en place de la liste dans le scope de smarty
	$convocations = $assemblee_generale->obtenirNombrePersonnesAJourDeCotisation($timestamp);
	$presences = $assemblee_generale->obtenirNombrePresencesEtPouvoirs($timestamp);
	$quorum = $assemblee_generale->obtenirEcartQuorum($timestamp);
    $liste_personnes = $assemblee_generale->obtenirListe($list_date_assemblee_generale, $list_ordre, $list_associatif);
    $liste_personnes_a_jour = $assemblee_generale->obtenirListePersonnesAJourDeCotisation($timestamp);
	$personnes_physiques = array();
    foreach ($liste_personnes as $liste_id => $personne) {
        $personnes_physiques[$liste_id] = $personne;
        $hash = md5($personne['id'] . '_' . $personne['email'] . '_' . $personne['login']);
        $personnes_physiques[$liste_id]['hash'] = $hash;
        if (in_array($personne['id'], $liste_personnes_a_jour)) {
            $personnes_physiques[$liste_id]['ajour'] = true;
        } else {
            $personnes_physiques[$liste_id]['ajour'] = false;
        }
    }
    $smarty->assign('convocations', $convocations);
    $smarty->assign('presences', $presences);
    $smarty->assign('quorum', $quorum);
    $smarty->assign('personnes', $personnes_physiques);

} elseif ($action == 'envoyer') {
    $formulaire = &instancierFormulaire();
    $timestamp = $assemblee_generale->obternirDerniereDate();
    $sujet = $assemblee_generale->preparerSujetDuMessage($timestamp);
    $corps = $assemblee_generale->preparerCorpsDuMessage($timestamp);
    $formulaire->setDefaults(array('sujet' => $sujet,
                                   'corps' => $corps));

    $formulaire->addElement('header'  , ''     , 'Message pour la convocation du ' . date('d/m/Y', $timestamp));
    $formulaire->addElement('text'    , 'sujet', 'Sujet');
    $formulaire->addElement('textarea', 'corps', 'Corps', array('cols' => 42, 'rows' => 10));

    $formulaire->addElement('header'  , 'boutons'            , '');
    $formulaire->addElement('submit'  , 'soumettre'          , ucfirst($action));

    $formulaire->addRule('sujet'      , 'Sujet manquant'     , 'required');
    $formulaire->addRule('corps'      , 'Corps manquant'   , 'required');

    if ($formulaire->validate()) {
        $ok = $assemblee_generale->envoyerConvocations($timestamp,
                                                       $formulaire->exportValue('sujet'),
                                                       $formulaire->exportValue('corps'));

        if ($ok) {
            AFUP_Logs::log('Envoi des emails de convocations aux personnes physiques pour l\'assemblée générale');
            afficherMessage('L\'envoi des emails de convocations aux personnes physiques pour l\'assemblée générale a été effectué', 'index.php?page=assemblee_generale&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de l\'envoi des emails aux personnes physiques pour l\'assemblée générale');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));

} elseif ($action == 'preparer') {
    $formulaire = &instancierFormulaire();
    $formulaire->setDefaults(array('date' => date("d/m/Y", time())));

    $formulaire->addElement('header'  , ''                   , 'Informations');
	$options = array('language' => 'fr', 'format' => 'd/m/Y', 'minYear' => 2005, 'maxYear' => date("Y") + 2);
	$formulaire->addElement('date'    , 'date', 'date de l\'AG', $options);

    $formulaire->addElement('header'  , 'boutons'            , '');
    $formulaire->addElement('submit'  , 'soumettre'          , ucfirst($action));

    $formulaire->addRule('date'       , 'Date manquante'     , 'required');

    if ($formulaire->validate()) {
        $ok = $assemblee_generale->preparer($formulaire->exportValue('date'));

        if ($ok !== false) {
            AFUP_Logs::log('Ajout de la préparation des personnes physiques à l\'assemblée générale');
            afficherMessage('La préparation des personnes physiques a été ajoutée', 'index.php?page=assemblee_generale&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de la préparation des personnes physiques');
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>
