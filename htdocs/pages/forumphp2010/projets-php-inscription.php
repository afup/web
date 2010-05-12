<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
require_once 'Afup/AFUP_AppelConferencier.php';


$formulaire = &instancierFormulaire();
$formulaire->setDefaults(array('civilite'            => 'M.',
));

$formulaire->addElement('hidden', 'id_forum', $config_forum['id']);
$annee_forum = $config_forum['annee'];
$groupe = array();


for ($i = 1;$i < 2; $i++) {
  $formulaire->addElement('header', null, 'Le projet');

  $formulaire->addElement('text', 'pres' . $i . '_titre', 'Nom', array('size' => 40, 'maxlength' => 80));
  $formulaire->addElement('textarea', 'pres' . $i . '_abstract', 'Description', array('cols' => 60, 'rows' => 10));
  $formulaire->addElement('file', 'logo'. $i, 'Logo du projet (vectoriel si possible)'     );
}

$formulaire->addRule('pres1_titre' , 'Nom manquant', 'required');
$formulaire->addRule('pres1_abstract'      , 'Description manquante'             , 'required');

$formulaire->addElement('header', null, 'Coordonnées des participants (deux au maximum)');

for ($i = 1; $i < 3; $i++) {
  $formulaire->addElement('select', 'civilite' . $i   , 'Civilité'       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
  $formulaire->addElement('text'  , 'nom' . $i        , 'Nom'            , array('size' => 30, 'maxlength' => 40));
  $formulaire->addElement('text'  , 'prenom' . $i     , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
  $formulaire->addElement('text'  , 'email' . $i      , 'Email'          , array('size' => 30, 'maxlength' => 100));
  $formulaire->addElement('text'  , 'telephone' . $i  , 'Tél.'           , array('size' => 20, 'maxlength' => 20));
  $formulaire->addElement('text'  , 'societe' . $i    , 'Société'        , array('size' => 50, 'maxlength' => 100));
  $formulaire->addElement('textarea', 'biographie' . $i, 'Biographie', array('cols' => 60, 'rows' => 5));
  $formulaire->addElement('file', 'photo' . $i, 'Photo (jpg, 90x120)'     );
}

$formulaire->addElement('header', null, 'Vous avez terminé');
$formulaire->addElement('submit', 'soumettre', 'Soumettre');


$formulaire->addRule('civilite1' , 'Civilité non sélectionnée', 'required');
$formulaire->addRule('nom1'      , 'Nom manquant'             , 'required');
$formulaire->addRule('prenom1'   , 'Prénom manquant'          , 'required');
$formulaire->addRule('email1'    , 'Email manquant'           , 'required');
$formulaire->addRule('email1'    , 'Email invalide'           , 'email');

if ($formulaire->validate()) {
  $valeurs = $formulaire->exportValues();

  $conf = new AFUP_AppelConferencier($bdd);

  // traiter les conferenciers
  for ($i = 1; $i < 3; $i++) {
    $check = trim($valeurs['nom' . $i]);
    if (empty($check)) {
      continue;
    }
    $var = 'conferencier' . $i;
    $$var = $conf->ajouterConferencier(
    $valeurs['id_forum'], $valeurs['civilite' . $i], $valeurs['nom' . $i], $valeurs['prenom' . $i],
    $valeurs['email' . $i], $valeurs['societe' . $i], $valeurs['biographie' . $i]
    );
    $file =& $formulaire->getElement('photo' . $i);
    if($file)
    {
      $file->moveUploadedFile(AFUP_CHEMIN_RACINE . 'templates/forumphp'.$annee_forum.'/images/intervenants',$$var.'.jpg');
    }
  }

  // ajouter les sessions
  for ($i = 1; $i < 4; $i++) {
    if (empty($valeurs['pres' . $i . '_titre'])) {
      continue;
    }

    $var = 'session' . $i;

    $$var = $conf->ajouterSession($config_forum['id'],date('Y-m-d'),
    $valeurs['pres' . $i . '_titre'],
    $valeurs['pres' . $i . '_abstract'],
    3,
    9
    );
    $file =& $formulaire->getElement('logo' . $i);
    if($file)
    {
      $file_value = $file->getValue();
      $file->moveUploadedFile(AFUP_CHEMIN_RACINE . 'templates/forumphp'.$annee_forum.'/images/projets',$$var.'_'.$file_value['name']);
    }
    if ($$var === false) {
      $smarty->assign('erreur', 'Une erreur est survenue lors de votre inscription.<br />Veuillez contacter le responsable des inscriptions afin de régler le problème.');
    } else {
      $conf->lierConferencierSession($conferencier1, $$var);
      if (isset($conferencier2)) {
        $conf->lierConferencierSession($conferencier2, $$var);
      }
      //$conf->envoyerEmail($$var);
    }
  }
  $smarty->display('soumission_engistree.html');
  exit(0);
}
$smarty->assign('formulaire', genererFormulaire($formulaire));

$smarty->display('projets-php-inscription.html');
?>
