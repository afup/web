<?php

// Impossible to access the file itself
use Afup\Site\Utils\Logs;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$formulaire = instancierFormulaire();
$defaults = $conf->exporter();

$formulaire->setDefaults($defaults);

$formulaire->addElement('header'  , ''                   , 'AFUP');
$formulaire->addElement('text'    , 'afup|raison_sociale', 'Raison Sociale', array('size' => 30));
$formulaire->addElement('textarea', 'afup|adresse'       , 'Adresse'       , array('cols' => 42, 'rows' => 7));
$formulaire->addElement('text'	  , 'afup|code_postal'   , 'Code postal'   , array('size' =>  5));
$formulaire->addElement('text'	  , 'afup|ville'         , 'Ville'         , array('size' => 20));
$formulaire->addElement('text'	  , 'afup|email'         , 'Email'         , array('size' => 30));

$formulaire->addElement('header'  , ''                       , 'Planète PHP FR');
$formulaire->addElement('textarea', 'planete|pertinence'     , 'Critère de pertinence', array('cols' => 42, 'rows' => 7));

$formulaire->addElement('header', 'boutons'  , '');
$formulaire->addElement('submit', 'soumettre', 'Enregistrer');

if ($formulaire->validate()) {
	$valeurs = $formulaire->exportValues();
    $conf->importer($valeurs);
    if ($conf->enregistrer()) {
        Logs::log('Modification de la configuration');
        afficherMessage('La configuration a été enregistrée', 'index.php?page=configuration');
    } else {
        $smarty->assign('erreur', "Une erreur est survenue lors de l'enregistrement de la configuration");
    }
}

$smarty->assign('formulaire', genererFormulaire($formulaire));

?>
