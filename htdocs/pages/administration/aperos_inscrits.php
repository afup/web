<?php

// Impossible to access the file itself
use Afup\Site\Aperos\Inscrits;
use Afup\Site\Aperos\Villes;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer'));
$tris_valides = array('nom', 'pseudo', 'prenom', 'etat');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

$inscrits = new Inscrits($bdd);

$villes = new Villes($bdd);

if ($action == 'lister') {

    // Valeurs par dfaut des paramtres de tri
    $list_ordre = 'pseudo';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramtres de tri en fonction des demandes passes en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }

    // Mise en place de la liste dans le scope de smarty
    $inscrits = $inscrits->obtenirListe($list_ordre, $list_associatif, $list_filtre);
    $smarty->assign('inscrits', $inscrits);

} elseif ($action == 'supprimer') {
    if ($inscrits->supprimer($_GET['id'])) {
        Logs::log('Suppression de l\'inscrit ' . $_GET['id'] . ' aux apéros PHP');
        afficherMessage('L\'inscrit aux apéros PHP a été supprimé', 'index.php?page=aperos_inscrits&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'inscrit aux apéros PHP', 'index.php?page=aperos_inscrits&action=lister', true);
    }

} else {
	$formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(array('validation' => '0'));
    } else {
        $champs = $inscrits->obtenir($_GET['id']);
        unset($champs['mot_de_passe']);
        $champs['date_inscription'] = $champs['date_inscription'] ? date("d/m/Y", $champs['date_inscription']) : "";
        $formulaire->setDefaults($champs);
    }

    $formulaire->addElement('header', '', 'Inscrit');
    $formulaire->addElement('text', 'nom', 'Nom', array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text', 'prenom', 'Prénom', array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text', 'pseudo', 'Pseudo', array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('password', 'mot_de_passe', 'Mot de passe', array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text', 'email', 'Email', array('size' => 30, 'maxlength' => 40));

    $formulaire->addElement('header', '', 'Informations');
    $formulaire->addElement('text', 'site_web', 'Site web', array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('select', 'id_ville', 'Ville', array(0 => '--') + $villes->obtenirListe('nom ASC', true));
    $formulaire->addElement('select', 'etat', 'Etat', $inscrits->obtenirListeEtat());
    $formulaire->addElement('static', 'date_inscription', 'Date d\'inscription');

    $formulaire->addElement('header', 'boutons', '');
    $formulaire->addElement('submit', 'soumettre', ucfirst($action));

    if ($formulaire->validate()) {
        if ($action == 'ajouter') {
            $ok = $inscrits->ajouter($formulaire->exportValue('pseudo'),
                                            $formulaire->exportValue('mot_de_passe'),
                                            $formulaire->exportValue('nom'),
                                            $formulaire->exportValue('prenom'),
                                            $formulaire->exportValue('email'),
                                            $formulaire->exportValue('site_web'),
                                            $formulaire->exportValue('id_ville'),
                                            $formulaire->exportValue('etat'));
        } else {
            $ok = $inscrits->modifier($_GET['id'],
                                             $formulaire->exportValue('pseudo'),
                                             $formulaire->exportValue('mot_de_passe'),
                                             $formulaire->exportValue('nom'),
                                             $formulaire->exportValue('prenom'),
                                             $formulaire->exportValue('email'),
                                             $formulaire->exportValue('site_web'),
                                             $formulaire->exportValue('id_ville'),
                                             $formulaire->exportValue('etat'));
        }

        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout de l\'inscrit ' . $formulaire->exportValue('pseudo') . ' aux apéros PHP ');
            } else {
                Logs::log('Modification de l\'inscrit ' . $formulaire->exportValue('pseudo') . ' (' . $_GET['id'] . ') aux apéros PHP ');
            }
            afficherMessage('L\'inscrit aux apéros PHP a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=aperos_inscrits&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'inscrit aux apéros PHP ', true);
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>