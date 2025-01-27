<?php

declare(strict_types=1);

use Afup\Site\Comptabilite\Comptabilite;
use Afup\Site\Utils\Logs;
use AppBundle\Model\ComptaModeReglement;

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'ajouter', 'modifier']);

$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);

if ($action === 'lister') {
    $data = $compta->obtenirListRegles(true);
    $smarty->assign('data', $data);
} elseif ($action === 'ajouter' || $action === 'modifier') {
    $formulaire = instancierFormulaire();

    if ($action === 'modifier') {
        $champs = $compta->obtenirListRegles('', (int) $_GET['id']);
        $formulaire->setDefaults($champs);

        $formulaire->addElement('hidden', 'id', (int) $_GET['id']);
    }

    // partie saisie
    $formulaire->addElement('header', '', '');
    $formulaire->addElement('text', 'label', 'Nom de la règle', ['size' => 30, 'maxlength' => 255]);
    $formulaire->addElement('text', 'condition', 'Condition', ['size' => 30, 'maxlength' => 255]);
    $formulaire->addElement('select', 'is_credit', 'Crédit/débit ?', [null => 'Les deux', '1' => 'Crédit', '0' => 'Débit']);
    $formulaire->addElement('select', 'mode_regl_id', 'Mode de règlement', [null => 'N.C.'] + ComptaModeReglement::list());
    $formulaire->addElement('select', 'vat', 'Taux de TVA', [null => 'N.C.', '0' => '0%', '5_5' => '5.5%', '10' => '10%', '20' => '20%']);
    $formulaire->addElement('select', 'category_id', 'Catégorie', $compta->obtenirListCategories());
    $formulaire->addElement('select', 'event_id', 'Évènement', $compta->obtenirListEvenements());
    $formulaire->addElement('select', 'attachment_required', 'Justificatif obligatoire ?', [null => 'N.C.', '1' => 'Oui', '0' => 'Non']);

    $formulaire->addRule('label' , 'Nom manquant' , 'required');
    $formulaire->addRule('condition' , 'Condition manquante' , 'required');

    // boutons
    $formulaire->addElement('header', 'boutons', '');
    $formulaire->addElement('submit', 'soumettre', ucfirst($action));


    if ($formulaire->validate()) {
        $valeur = $formulaire->exportValues();

        if ($action === 'ajouter') {
            $ok = $compta->ajouterRegle($valeur['label'], $valeur['condition'], $valeur['is_credit'], $valeur['vat'], $valeur['category_id'], $valeur['event_id'], $valeur['mode_regl_id'], $valeur['attachment_required']);
        } else {
            $ok = $compta->modifierRegle($valeur['id'], $valeur['label'], $valeur['condition'], $valeur['is_credit'], $valeur['vat'], $valeur['category_id'], $valeur['event_id'], $valeur['mode_regl_id'], $valeur['attachment_required']);
        }

        if ($ok) {
            if ($action === 'ajouter') {
                Logs::log('Ajout une règle ' . $formulaire->exportValue('label'));
            } else {
                Logs::log('Modification une règle ' . $formulaire->exportValue('label') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage(
                'La règle a été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'),
                'index.php?page=compta_conf_regle&action=lister'
            );
        } else {
            $smarty->assign(
                'erreur',
                'Une erreur est survenue lors de ' . (($action === 'ajouter') ? "l'ajout" : 'la modification') . ' de la règle'
            );
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
