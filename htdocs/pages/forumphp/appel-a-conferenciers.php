<?php
require_once '../../include/prepend.inc.php';

require_once 'Afup/AFUP_AppelConferencier.php';

$formulaire = &instancierFormulaire();
$formulaire->setDefaults(array('civilite'            => 'M.',
                        ));

$formulaire->addElement('hidden', 'id_forum', 3);

$formulaire->addElement('header', null, 'Coordonnées du conférencier');
$groupe = array();

$formulaire->addElement('select', 'civilite'   , 'Civilité'       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
$formulaire->addElement('text'  , 'nom'        , 'Nom (last name)'            , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'  , 'prenom'     , 'Prénom (first name)'         , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'  , 'email'      , 'Email'          , array('size' => 30, 'maxlength' => 100));
$formulaire->addElement('text'  , 'telephone'  , 'Tél.'           , array('size' => 20, 'maxlength' => 20));
$formulaire->addElement('text'  , 'societe'    , 'Société (company)'        , array('size' => 50, 'maxlength' => 100));

for ($i = 1;$i < 4; $i++) {
    $formulaire->addElement('header', null, 'Présentation ' . $i);

    $formulaire->addElement('text', 'pres' . $i . '_titre', 'Titre de la présentation (presentation title)', array('size' => 40, 'maxlength' => 80));
    $formulaire->addElement('textarea', 'pres' . $i . '_abstract', 'Résumé de la présentation (presentation abstract)', array('cols' => 60, 'rows' => 10));

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Journée fonctionnelle', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Journée technique'    , 2);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Les deux'             , 3);
    $formulaire->addGroup($groupe, 'groupe_pres' . $i, "S'applique à la journée (what day it applies to)", '<br />', false);
}

$formulaire->addElement('header', null, 'Vous avez terminé');

$formulaire->addElement('submit', 'soumettre', 'Soumettre');

$formulaire->addGroupRule('groupe_pres1', 'Selectionnez la journée à laquelle votre conférence s\'applique' , 'required', null, 1);
$formulaire->addRule('pres1_titre', 'Titre manquant', 'required');
$formulaire->addRule('pres1_abstract', 'Résumé manquant', 'required');

$formulaire->addRule('civilite'               , 'Civilité non sélectionnée', 'required');
$formulaire->addRule('nom'                    , 'Nom manquant'             , 'required');
$formulaire->addRule('prenom'                 , 'Prénom manquant'          , 'required');
$formulaire->addRule('email'                  , 'Email manquant'           , 'required');
$formulaire->addRule('email'                  , 'Email invalide'           , 'email');

if ($formulaire->validate()) {
    $valeurs = $formulaire->exportValues();

    $conf = new AFUP_AppelConferencier($bdd);

    // traiter le conferencier
    $conferencier_id = $conf->ajouterConferencier(
        $valeurs['id_forum'], $valeurs['civilite'], $valeurs['nom'], $valeurs['prenom'],
        $valeurs['email'], $valeurs['societe']
    );

    // ajouter les sessions
    for ($i = 1; $i <= 3; $i++) {
        if (empty($valeurs['pres' . $i . '_titre'])) {
            continue;
        }

        $ajout = $conf->ajouterSession($conferencier_id, date('Y-m-d'),
                              $valeurs['pres' . $i . '_titre'],
                              $valeurs['pres' . $i . '_abstract'],
                              $valeurs['pres' . $i . '_journee']
        );

        if ($ajout === false) {
            $smarty->assign('erreur', 'Une erreur est survenue lors de votre inscription.<br />Veuillez contacter le responsable des inscriptions afin de régler le problème.');
        } else {
            $smarty->display('soumission_engistree.html');
        }
    }
    exit(0);
}
$smarty->assign('formulaire', genererFormulaire($formulaire));
$smarty->display('appel-a-conferenciers.html');
?>