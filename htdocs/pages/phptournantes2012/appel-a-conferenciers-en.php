<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';

$fin_de_lappel = $config_forum['date_fin_appel_conferencier'];

setlocale(LC_TIME, 'fr_FR');
$smarty->assign('date_fin_appel_fr', strftime('%A %d %B %Y à %H:%M:%S', $fin_de_lappel));
setlocale(LC_TIME, 'en_US');
$smarty->assign('date_fin_appel_en', strftime('%A %B %d, %Y at %H:%M:%S', $fin_de_lappel));

setlocale(LC_TIME, 'fr_FR');
if ((time() - $fin_de_lappel) > 0) {
    $smarty->display('fin_appel.html');
    exit();
}

$formulaire = &instancierFormulaire();
$formulaire->setDefaults(array('civilite'            => 'M.',
                        ));

$formulaire->addElement('hidden', 'id_forum', $config_forum['id']);

//$formulaire->addElement('header', null, 'Speakers informations (two maximum)');
$groupe = array();

for ($i = 1; $i < 3; $i++) {
	$html = $i == 1?'':' (optional)';
	$formulaire->addElement('header', null, 'Speaker '.$i.$html);
    $formulaire->addElement('select', 'civilite' . $i   , 'Civility'       , array('M.' => 'Mr', 'Mme' => 'Mrs', 'Mlle' => 'Miss'));
    $formulaire->addElement('text'  , 'nom' . $i        , 'Last name'            , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'  , 'prenom' . $i     , 'First name'         , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'  , 'email' . $i      , 'Email'          , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'telephone' . $i  , 'Phone'           , array('size' => 20, 'maxlength' => 20));
    $formulaire->addElement('text'  , 'societe' . $i    , 'Company'        , array('size' => 50, 'maxlength' => 100));
    $formulaire->addElement('textarea', 'biographie' . $i, 'Biography', array('cols' => 60, 'rows' => 5));
}

for ($i = 1;$i < 4; $i++) {
    $formulaire->addElement('header', null, 'Presentation ' . $i);

    $formulaire->addElement('text', 'pres' . $i . '_titre', 'Title', array('size' => 40, 'maxlength' => 80));
    $formulaire->addElement('textarea', 'pres' . $i . '_abstract', 'Abstract', array('cols' => 60, 'rows' => 10));

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Functional', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Technical'    , 2);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Both'             , 3);
    $formulaire->addGroup($groupe, 'groupe_pres' . $i, "Audience", '<br />', false);

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, 'Conference', 1);
    //$groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, 'Workshop'    , 2);
    $formulaire->addGroup($groupe, 'groupe_type_pres' . $i, "Type of session", '<br />', false);
}

$formulaire->addElement('header', null, 'You have finished');

$formulaire->addElement('submit', 'soumettre', 'Submit');

$formulaire->addGroupRule('groupe_pres1', 'Select audience' , 'required', null, 1);
$formulaire->addRule('pres1_titre', 'Title is missing', 'required');
$formulaire->addRule('pres1_abstract', 'Abstract is missing', 'required');

$formulaire->addGroupRule('groupe_type_pres1', 'Select type of session' , 'required', null, 1);
$formulaire->addRule('pres1_genre', 'Type of session is missing', 'required');

$formulaire->addRule('civilite1' , 'Civility is missing', 'required');
$formulaire->addRule('nom1'      , 'Last name is missing'             , 'required');
$formulaire->addRule('prenom1'   , 'First name is missing'          , 'required');
$formulaire->addRule('email1'    , 'Email is missing'           , 'required');
$formulaire->addRule('email1'    , 'Invalid email'           , 'email');

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
            $valeurs['pres' . $i . '_journee'],
            $valeurs['pres' . $i . '_genre']
        );

        if ($$var === false) {
            $smarty->assign('erreur', 'An error is occured during your subscription.<br />Please contact us to solve this problem.');
        } else {
            $conf->lierConferencierSession($conferencier1, $$var);
            if (isset($conferencier2)) {
                $conf->lierConferencierSession($conferencier2, $$var);
            }
            $conf->envoyerEmail($$var);
        }
    }
    $smarty->display('soumission_engistree.html');
    exit(0);
}
$smarty->assign('formulaire', genererFormulaire($formulaire));
$smarty->display('appel-a-conferenciers-en.html');
?>
