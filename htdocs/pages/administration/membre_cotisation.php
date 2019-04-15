<?php

// Impossible to access the file itself
use Afup\Site\Association\Cotisations;
use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('payer', 'telecharger_facture', 'envoyer_facture'));
$smarty->assign('action', $action);

$personnes_physiques = new Personnes_Physiques($bdd);

$pays = new Pays($bdd);

$formulaire = instancierFormulaire();

$identifiant = $droits->obtenirIdentifiant();
$champs = $personnes_physiques->obtenir($identifiant);
$cotisation = $personnes_physiques->obtenirDerniereCotisation($identifiant);
unset($champs['mot_de_passe']);
$cotisations = new Cotisations($bdd, $droits);


if (!$cotisation) {
    $message = empty($_GET['hash'])? 'Est-ce vraiment votre première cotisation ?' : '';
} else {
    $endSubscription = $cotisations->finProchaineCotisation($cotisation);
    $message = sprintf(
        'Votre dernière cotisation -- %s %s -- est valable jusqu\'au %s. <br />
        Si vous renouvellez votre cotisation maintenant, celle-ci sera valable jusqu\'au %s',
        $cotisation['montant'],
        EURO,
        date("d/m/Y", $cotisation['date_fin']),
        $endSubscription->format('d/m/Y')
    );
}


if (isset($_GET['action']) && in_array($_GET['action'], ['envoyer_facture', 'telecharger_facture'])) {
    if (false === $cotisations->isCurrentUserAllowedToReadInvoice ($_GET['id'])) {
        Logs::log('L\'utilisateur id: ' . $identifiant . ' a tenté de voir la facture id:' . $_GET['id'] . ' de l\'utilisateur id:' . $_GET['id_personne']);
        afficherMessage(null, 'index.php?page=membre_cotisation', 'Cette facture ne vous appartient pas, vous ne pouvez la visualiser.');
    } elseif ($_GET['action'] == 'envoyer_facture') {
        if ($cotisations->envoyerFacture($_GET['id'], $this->get(\Afup\Site\Utils\Mail::class))) {
            Logs::log('Envoi par email de la facture pour la cotisation n°' . $_GET['id']);
            afficherMessage('La facture a été envoyée par mail', 'index.php?page=membre_cotisation');
        } else {
            afficherMessage("La facture n'a pas pu être envoyée par mail", 'index.php?page=membre_cotisation', true);
        }
    } elseif ($_GET['action'] == 'telecharger_facture') {
        $cotisations->genererFacture($_GET['id']);
        die();
    }
}

$formulaire->addElement('header' , '' , 'Paiement');
$groupe = array();
if ($champs['id_personne_morale'] > 0) {
    $id_personne = $champs['id_personne_morale'];

    $personne_morale = new \Afup\Site\Association\Personnes_Morales($bdd);

    $type_personne = AFUP_PERSONNES_MORALES;
    $groupe[] = $formulaire->createElement('radio', 'type_cotisation', null, 'Personne morale : <strong>' . $personne_morale->getMembershipFee($id_personne) . ',00 ' . EURO . '</strong>', AFUP_COTISATION_PERSONNE_MORALE);
    $formulaire->setDefaults(array('type_cotisation' => AFUP_COTISATION_PERSONNE_MORALE));
    $montant = $personne_morale->getMembershipFee($id_personne);
} else {
    $id_personne = $identifiant;
    $type_personne = AFUP_PERSONNES_PHYSIQUES;
    $groupe[] = $formulaire->createElement('radio', 'type_cotisation', null, 'Personne physique : <strong>' . AFUP_COTISATION_PERSONNE_PHYSIQUE . ',00 ' . EURO . '</strong>' , AFUP_COTISATION_PERSONNE_PHYSIQUE);
    $formulaire->setDefaults(array('type_cotisation' => AFUP_COTISATION_PERSONNE_PHYSIQUE));
    $montant = AFUP_COTISATION_PERSONNE_PHYSIQUE;
}
$formulaire->addGroup($groupe, 'type_cotisation', 'Type de cotisation', '<br />', false);
$formulaire->addRule('type_cotisation' , 'Type de cotisation manquant' , 'required');

$donnees = $personnes_physiques->obtenir($identifiant);

$reference = (new \AppBundle\Association\MembershipFeeReferenceGenerator())->generate(new \DateTimeImmutable('now'), $type_personne, $id_personne, $donnees['nom']);

$paybox = $this->get(\AppBundle\Payment\PayboxFactory::class)->createPayboxForSubscription(
    $reference,
    (float) $montant,
    $donnees['email']
);

$smarty->assign('paybox', $paybox);
$smarty->assign('message', $message);
$smarty->assign('formulaire', genererFormulaire($formulaire));

$cotisation_physique = $cotisations->obtenirListe(0 , $donnees['id']);
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
