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
$smarty->assign('action', $action);

$compta = new Comptabilite($bdd);


if ($action == 'lister') {
    $data = $compta->obtenirListComptes(true);
    $smarty->assign('data', $data);
} elseif ($action == 'ajouter' || $action == 'modifier') {
    $formulaire = instancierFormulaire();

    if ($action === 'modifier') {
        $champsRecup = $compta->obtenirListComptes('',$_GET['id']);
        $champs['nom_compte']          = $champsRecup['nom_compte'];

        $formulaire->setDefaults($champs);

        $formulaire->addElement('hidden', 'id', $_GET['id']);
    }

    // partie saisie
    $formulaire->addElement('header'  , ''                         , '');
    $formulaire->addElement('text', 'nom_compte', 'Compte' , ['size' => 30, 'maxlength' => 40]);


    // boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));


    if ($formulaire->validate()) {
        $valeur = $formulaire->exportValues();


        if ($action === 'ajouter') {
            $ok = $compta->ajouterConfig(
                                    'compta_compte',
                                    'nom_compte',
                                    $valeur['nom_compte'],
                                    );
        } else {
            dump($valeur);
            $ok = $compta->modifierConfig(
                                    'compta_compte',
                                       $valeur['id'],
                                       'nom_compte',
                                       $valeur['nom_compte'],
                                     );
        }

        if ($ok) {
            if ($action === 'ajouter') {
                Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('L\'écriture a été ' . (($action === 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=compta_conf_compte&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action === 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
