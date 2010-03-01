<?php
$action = verifierAction(array('payer', 'telecharger_facture', 'envoyer_facture'));
$smarty->assign('action', $action);

require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Personnes_Morales.php';
require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Personnes_Physiques.php';
$personnes_physiques = new AFUP_Personnes_Physiques($bdd);

require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Pays.php';
$pays = new AFUP_Pays($bdd);

$formulaire = &instancierFormulaire();

$identifiant = $droits->obtenirIdentifiant();
$champs = $personnes_physiques->obtenir($identifiant);
$cotisation = $personnes_physiques->obtenirDerniereCotisation($identifiant);
unset($champs['mot_de_passe']);

if (!$cotisation) {
    $message = empty($_GET['hash'])? 'Est-ce vraiment votre première cotisation ?' : '';
} else {
    $message = 'Votre dernière cotisation -- ' . $cotisation['montant'] . ' ' . EURO . ' -- est valable jusqu\'au ' . date("d/m/Y", $cotisation['date_fin']) . '.';
}

if (isset($_GET['action']) && $_GET['action'] == 'envoyer_facture') {
    $cotisations = new AFUP_Cotisations($bdd);
    if ($cotisations->envoyerFacture($_GET['id'])) {
        AFUP_Logs::log('Envoi par email de la facture pour la cotisation n°' . $_GET['id']);
        afficherMessage('La facture a été envoyée par mail', 'index.php?page=membre_cotisation');
    } else {
        afficherMessage("La facture n'a pas pu être envoyée par mail", 'index.php?page=membre_cotisation', true);
    }
} elseif (isset($_GET['action']) && $_GET['action'] == 'telecharger_facture') {
    $cotisations = new AFUP_Cotisations($bdd);
    $cotisations->genererFacture($_GET['id']);
    die();
}

$formulaire->addElement('header' , '' , 'Paiement');
$groupe = array();
if ($champs['id_personne_morale'] > 0) {
    $id_personne = $champs['id_personne_morale'];
    $type_personne = AFUP_PERSONNES_MORALES;
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_cotisation', null, 'Personne morale : <strong>50,00 ' . EURO . '</strong>', AFUP_COTISATION_PERSONNE_MORALE);
    $formulaire->setDefaults(array('type_cotisation' => AFUP_COTISATION_PERSONNE_MORALE));
    $montant = AFUP_COTISATION_PERSONNE_MORALE;
} else {
    $id_personne = $identifiant;
    $type_personne = AFUP_PERSONNES_PHYSIQUES;
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_cotisation', null, 'Personne physique : <strong>20,00 ' . EURO . '</strong>' , AFUP_COTISATION_PERSONNE_PHYSIQUE);
    $formulaire->setDefaults(array('type_cotisation' => AFUP_COTISATION_PERSONNE_PHYSIQUE));
    $montant = AFUP_COTISATION_PERSONNE_PHYSIQUE;
}
$formulaire->addGroup($groupe, 'type_cotisation', 'Type de cotisation', '<br />', false);
$formulaire->addRule('type_cotisation' , 'Type de cotisation manquant' , 'required');

$donnees = $personnes_physiques->obtenir($identifiant);

$reference = strtoupper('C' . date('Y') . '-' . date('dmYHi') . '-' . $type_personne . '-' . $id_personne . '-' . substr($donnees['nom'], 0, 5));
$reference = supprimerAccents($reference);
$reference = preg_replace('/[^A-Z0-9_\-\:\.;]/', '', $reference);
$reference .= '-' . strtoupper(substr(md5($reference), - 3));

require_once 'paybox/payboxv2.inc';
$paybox = new PAYBOX;
$paybox->set_langue('FRA'); // Langue de l'interface PayBox
$paybox->set_site($conf->obtenir('paybox|site'));
$paybox->set_rang($conf->obtenir('paybox|rang'));
$paybox->set_identifiant('83166771');

$paybox->set_total($montant * 100); // Total de la commande, en centimes d'euros
$paybox->set_cmd($reference); // Référence de la commande
$paybox->set_porteur($donnees['email']); // Email du client final (Le porteur de la carte)

// URL en cas de reussite
$paybox->set_effectue('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_effectue.php');
// URL en cas de refus du paiement
$paybox->set_refuse('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_refuse.php');
// URL en cas d'annulation du paiement de la part du client
$paybox->set_annule('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_annule.php');
// URL en cas de disfonctionnement de PayBox
$paybox->set_erreur('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_erreur.php');

$paybox->set_wait(50000); // Délai d'attente avant la redirection
$paybox->set_boutpi('R&eacute;gler par carte'); // Texte du bouton
$paybox->set_bkgd('#FAEBD7'); // Fond de page
$paybox->set_output('B'); // On veut gerer l'affichage dans la page intermediaire
if (preg_match('#<CENTER>.*</b>(.*)</CENTER>#is', $paybox->paiement(), $r)) {
    $smarty->assign('paybox', $r[1]);
} else {
    $smarty->assign('paybox', '');
}

$smarty->assign('message', $message);
$smarty->assign('formulaire', genererFormulaire($formulaire));

$cotisations = new AFUP_Cotisations($bdd);
$cotisation_physique = $cotisations->obtenirListe(0 , $donnees['id']);

$cotisations = new AFUP_Cotisations($bdd);
$cotisation_morale = $cotisations->obtenirListe(1 , $donnees['id_personne_morale']);

if (is_array($cotisation_morale) && is_array($cotisation_physique)) {
    $cotisations = array_merge($cotisation_physique, $cotisation_morale);
} elseif (is_array($cotisation_morale)) {
    $cotisations = $cotisation_morale;
} elseif (is_array($cotisation_physique)) {
    $cotisations = $cotisation_physique;
} else {
    $cotisations = array();
}

$smarty->assign('liste_cotisations', $cotisations);
$smarty->assign('time', time());

?>