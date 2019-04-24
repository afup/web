<?php
use Afup\Site\Association\Partenariat;
use Afup\Site\Utils\Logs;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';




Logs::initialiser($bdd, 0);

$partenariat = new Partenariat($bdd);

$formulaire = instancierFormulaire();

$formulaire->addElement('header'  , ''  , 'Vérifier l\'existence d\'un membre');
$formulaire->addElement('text', 'nom', 'Nom');
$formulaire->addElement('text', 'prenom', 'Prénom');
$formulaire->addElement('header' , 'boutons' , '');
$formulaire->addElement('submit'  , 'verifier' , 'Vérifier');
$formulaire->addRule('nom' , 'Nom manquant' , 'required');
$formulaire->addRule('prenom' , 'Prénom manquant' , 'required');

if ($formulaire->validate()) {
	Logs::log('Vérification par un partenaire de : '.$formulaire->exportValue('nom').' - '.$formulaire->exportValue('prenom'));
	$smarty->assign(
		'resultat',
		$partenariat->verifierMembre(
			$formulaire->exportValue('nom'),
			$formulaire->exportValue('prenom')
		)
	);
}

$smarty->assign('formulaire', genererFormulaire($formulaire));
$smarty->display('membre.html');
