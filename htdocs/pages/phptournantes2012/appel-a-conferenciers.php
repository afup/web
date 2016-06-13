<?php
use Afup\Site\Forum\AppelConferencier;

require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';


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

//$formulaire->addElement('header', null, 'Coordonnées des conférenciers (deux au maximum)');
$groupe = array();

for ($i = 1; $i < 3; $i++) {
	$html = $i == 1?'':' (optionnel)';
	$formulaire->addElement('header', null, 'Conférencier '.$i.$html);
	$formulaire->addElement('select', 'civilite' . $i   , 'Civilité'       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
    $formulaire->addElement('text'  , 'nom' . $i        , 'Nom'            , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'  , 'prenom' . $i     , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'  , 'email' . $i      , 'Email'          , array('size' => 30, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'telephone' . $i  , 'Tél.'           , array('size' => 20, 'maxlength' => 20));
    $formulaire->addElement('text'  , 'societe' . $i    , 'Société'        , array('size' => 50, 'maxlength' => 100));
    $formulaire->addElement('textarea', 'biographie' . $i, 'Biographie', array('cols' => 60, 'rows' => 5));
}

for ($i = 1;$i < 4; $i++) {
    $formulaire->addElement('header', null, 'Présentation ' . $i);

    $formulaire->addElement('text', 'pres' . $i . '_titre', 'Titre', array('size' => 40, 'maxlength' => 80));
    $formulaire->addElement('textarea', 'pres' . $i . '_abstract', 'Résumé', array('cols' => 60, 'rows' => 10));

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Fonctionnel', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Technique'    , 2);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Les deux'             , 3);
    $formulaire->addGroup($groupe, 'groupe_pres' . $i, "Public visé", '<br />', false);

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, 'Conférence plénière', 1);
    //$groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, 'Atelier'    , 2);
    $formulaire->addGroup($groupe, 'groupe_type_pres' . $i, "Type de session", '<br />', false);
}

$formulaire->addElement('header', null, 'Vous avez terminé');

$formulaire->addElement('submit', 'soumettre', 'Soumettre');

$formulaire->addGroupRule('groupe_pres1', 'Selectionnez le public visé' , 'required', null, 1);
$formulaire->addRule('pres1_titre', 'Titre manquant', 'required');
$formulaire->addRule('pres1_abstract', 'Résumé manquant', 'required');

$formulaire->addGroupRule('groupe_type_pres1', 'Indiquez le type de session' , 'required', null, 1);
$formulaire->addRule('pres1_genre', 'Type de session manquant', 'required');

$formulaire->addRule('civilite1' , 'Civilité non sélectionnée', 'required');
$formulaire->addRule('nom1'      , 'Nom manquant'             , 'required');
$formulaire->addRule('prenom1'   , 'Prénom manquant'          , 'required');
$formulaire->addRule('email1'    , 'Email manquant'           , 'required');
$formulaire->addRule('email1'    , 'Email invalide'           , 'email');

if ($formulaire->validate()) {
    $valeurs = $formulaire->exportValues();

    $conf = new AppelConferencier($bdd);

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
            $smarty->assign('erreur', 'Une erreur est survenue lors de votre inscription.<br />Veuillez contacter le responsable des inscriptions afin de régler le problème.');
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
$smarty->display('appel-a-conferenciers.html');
?>
