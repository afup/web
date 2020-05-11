<?php

// Impossible to access the file itself
use Afup\Site\Association\Cotisations;
use Afup\Site\Association\Personnes_Morales;
use Afup\Site\Utils\Logs;
use AppBundle\Association\Model\Repository\UserRepository;
use Assert\Assertion;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$userRepository = $this->get(UserRepository::class);

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer', 'telecharger_facture', 'envoyer_facture'));
$smarty->assign('action', $action);

// Personne


if ($_GET['type_personne'] == AFUP_PERSONNES_PHYSIQUES) {
    $user = $userRepository->get($_GET['id_personne']);
    Assertion::notNull($user);
    $personne = ['nom' => $user->getLastName(), 'prenom' => $user->getFirstName()];
} else {
    $personnes = new Personnes_Morales($bdd);
    $personne = $personnes->obtenir($_GET['id_personne']);
}
$smarty->assign('type_personne', $_GET['type_personne']);
$smarty->assign('id_personne'  , $_GET['id_personne']);
$smarty->assign('personne', $personne);

// Cotisations

$cotisations = new Cotisations($bdd);

if ($action == 'lister') {
    $smarty->assign('cotisations', $cotisations->obtenirListe($_GET['type_personne'], $_GET['id_personne']));
} elseif ($action == 'telecharger_facture'){
	$cotisations->genererFacture($_GET['id']);
} elseif ($action == 'envoyer_facture'){
	if($cotisations->envoyerFacture($_GET['id'], $this->get(\AppBundle\Email\Mailer\Mailer::class), $userRepository)){
	   Logs::log('Envoi par email de la facture pour la cotisation n°' . $_GET['id']);
       afficherMessage('La facture a été envoyée', 'index.php?page=cotisations&action=lister&type_personne=' . $_GET['type_personne'] . '&id_personne=' .$_GET['id_personne']);
	} else {
       afficherMessage("La facture n'a pas pu être envoyée", 'index.php?page=cotisations&action=lister&type_personne=' . $_GET['type_personne'] . '&id_personne=' .$_GET['id_personne'], true);
    }
} elseif ($action == 'supprimer') {
    if ($cotisations->supprimer($_GET['id'])) {
        Logs::log('Suppression de la cotisation ' . $_GET['id']);
        afficherMessage('La cotisation a été supprimée', 'index.php?page=cotisations&action=lister&type_personne=' . $_GET['type_personne'] . '&id_personne=' .$_GET['id_personne']);
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de la personne morale', 'index.php?page=personnes_morales&action=lister', true);
    }
} else {
    // Formulaire
    $formulaire = instancierFormulaire();

    if ($action == 'ajouter') {
        $date_debut = $cotisations->obtenirDateDebut($_GET['type_personne'], $_GET['id_personne']);
        $formulaire->setDefaults(array('date_debut' => $date_debut,
                                       'date_fin'   => strtotime('+1year', $date_debut)));
    } elseif ($action == 'modifier') {
        $formulaire->setDefaults($cotisations->obtenir($_GET['id']));
    }

    $formulaire->addElement('hidden', 'type_personne', $_GET['type_personne']);
    $formulaire->addElement('hidden', 'id_personne'  , $_GET['id_personne']);

    $formulaire->addElement('header' , ''                      , '');
    $formulaire->addElement('text'   , 'montant'               , 'Montant', array('size' => 5, 'maxlength' => 5));
    $formulaire->addElement('select' , 'type_reglement'        , 'Type règlement', array(null                                => '',
                                                                                         AFUP_COTISATIONS_REGLEMENT_ESPECES  => 'Espèces',
                                                                                         AFUP_COTISATIONS_REGLEMENT_CHEQUE   => 'Chèques',
                                                                                         AFUP_COTISATIONS_REGLEMENT_VIREMENT => 'Virement',
                                                                                         AFUP_COTISATIONS_REGLEMENT_ENLIGNE  => 'En ligne',
                                                                                         AFUP_COTISATIONS_REGLEMENT_AUTRE    => 'Autre'));
    $formulaire->addElement('text'   , 'informations_reglement', 'Informations', array('size' => 50, 'maxlength' => 255));
    $formulaire->addElement('date'   , 'date_debut'            , 'Date début', array('language' => 'fr',
                                                                                     'format'   => 'd F Y',
                                                                                     'minYear'  => 2002,
                                                                                     'maxYear'  => date('Y') + 5));
    $formulaire->addElement('date'   , 'date_fin'              , 'Date fin', array('language' => 'fr',
                                                                                   'format'   => 'd F Y',
                                                                                   'minYear'  => 2002,
                                                                                   'maxYear'  => date('Y') + 5));
    $formulaire->addElement('textarea', 'commentaires'         , 'Commentaires', array('cols' => 42, 'rows' => 5));

    $formulaire->addElement('header', 'boutons'          , '');
    $formulaire->addElement('submit', 'soumettre'        , ucfirst($action));

    $formulaire->addRule('montant'       , 'Montant manquant'                 , 'required');
    $formulaire->addRule('type_reglement', 'Type de réglement non sélectionné', 'required');

    if ($formulaire->validate()) {
        $nom        = ($_GET['type_personne'] == AFUP_PERSONNES_PHYSIQUES) ? $personne['prenom'] . ' ' . $personne['nom'] : $personne['raison_sociale'];
        $date_debut = $formulaire->exportValue('date_debut');
        $date_debut = mktime(0, 0, 0, $date_debut['F'], $date_debut['d'], $date_debut['Y']);
        $date_fin   = $formulaire->exportValue('date_fin');
        $date_fin   = mktime(0, 0, 0, $date_fin['F'], $date_fin['d'], $date_fin['Y']);

        if ($action == 'ajouter') {
            if ($cotisations->ajouter($formulaire->exportValue('type_personne'),
                                      $formulaire->exportValue('id_personne'),
                                      $formulaire->exportValue('montant'),
                                      $formulaire->exportValue('type_reglement'),
                                      $formulaire->exportValue('informations_reglement'),
                                      $date_debut,
                                      $date_fin,
                                      $formulaire->exportValue('commentaires'))) {
                Logs::log("Ajout de la cotisation jusqu'au " . date('d F Y', $date_fin) . ' pour ' . $nom);
                afficherMessage("La cotisation jusqu'au " . date('d F Y', $date_fin) . ' pour ' . $nom . ' a bien été ajoutée', 'index.php?page=cotisations&action=lister&type_personne=' . $_GET['type_personne'] . '&id_personne=' .$_GET['id_personne']);
            } else {
                $smarty->assign('erreur', "Une erreur est survenue lors de l'ajout de la cotisation jusqu'au " . date('d F Y', $date_fin) . ' pour ' . $nom);
            }
        } else {
            if ($cotisations->modifier($_GET['id'],
                                       $formulaire->exportValue('type_personne'),
                                       $formulaire->exportValue('id_personne'),
                                       $formulaire->exportValue('montant'),
                                       $formulaire->exportValue('type_reglement'),
                                       $formulaire->exportValue('informations_reglement'),
                                       $date_debut,
                                       $date_fin,
                                       $formulaire->exportValue('commentaires'))) {
                Logs::log('Modification de la cotisation (' . $_GET['id'] . ') pour ' . $nom);
                afficherMessage('La cotisation pour ' . $nom . ' a bien été modifiée', 'index.php?page=cotisations&action=lister&type_personne=' . $_GET['type_personne'] . '&id_personne=' .$_GET['id_personne']);
            } else {
                $smarty->assign('erreur', 'Une erreur est survenue lors de la modification de la cotisation (' . $_GET['id'] . ') pour ' . $nom);
            }
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>
