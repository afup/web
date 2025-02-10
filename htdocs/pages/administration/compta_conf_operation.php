<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Comptabilite\Comptabilite;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(['lister', 'ajouter', 'modifier']);
//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);


if ($action == 'lister') {
    $data = $compta->obtenirListOperations(true);
    $smarty->assign('data', $data);
} elseif ($action == 'ajouter' || $action == 'modifier') {
    $formulaire = instancierFormulaire();

    if ($action === 'modifier') {
        $champsRecup = $compta->obtenirListOperations('',$_GET['id']);
        $champs['operation']          = $champsRecup['operation'];

        $formulaire->setDefaults($champs);

        $formulaire->addElement('hidden', 'id', $_GET['id']);
    }

    // partie saisie
    $formulaire->addElement('header'  , ''                         , '');
    $formulaire->addElement('text', 'operation', 'Operation' , ['size' => 30, 'maxlength' => 40]);


    // boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));


    if ($formulaire->validate()) {
        $valeur = $formulaire->exportValues();


        if ($action === 'ajouter') {
            $ok = $compta->ajouterConfig(
                                    'compta_operation',
                                    'operation',
                                    $valeur['operation']
                                    );
        } else {
            $ok = $compta->modifierConfig(
                                    'compta_operation',
                                       $valeur['id'],
                                       'operation',
                                       $valeur['operation']
                                     );
        }

        if ($ok) {
            if ($action === 'ajouter') {
                Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('L\'écriture a été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=compta_conf_operation&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action === 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
