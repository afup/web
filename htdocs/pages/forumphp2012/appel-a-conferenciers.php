<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_AppelConferencier.php';

$fin_de_lappel = $config_forum['date_fin_appel_conferencier'];

setlocale(LC_TIME, 'fr_FR.utf8');
$smarty->assign('date_fin_appel_fr', strftime('%A %d %B %Y à %H:%M:%S', $fin_de_lappel));
setlocale(LC_TIME, 'en_US.utf8');
$smarty->assign('date_fin_appel_en', strftime('%A %B %d, %Y at %H:%M:%S', $fin_de_lappel));

setlocale(LC_TIME, 'fr_FR.utf8');
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
    $formulaire->addElement('text'  , 'nom' . $i        , 'Nom'            , array('size' => 30, 'maxlength' => 40, 'class' => 'span7'));
    $formulaire->addElement('text'  , 'prenom' . $i     , 'Prénom'         , array('size' => 30, 'maxlength' => 40, 'class' => 'span7'));
    $formulaire->addElement('text'  , 'email' . $i      , 'Email'          , array('size' => 30, 'maxlength' => 100, 'class' => 'span7'));
    $formulaire->addElement('text'  , 'telephone' . $i  , 'Tél.'           , array('size' => 20, 'maxlength' => 20, 'class' => 'span7'));
    $formulaire->addElement('text'  , 'societe' . $i    , 'Société'        , array('size' => 50, 'maxlength' => 100, 'class' => 'span7'));
    $formulaire->addElement('textarea', 'biographie' . $i, 'Biographie', array('cols' => 60, 'rows' => 5, 'class' => 'span7'));
    $formulaire->addElement('file'  , 'logo' . $i       , 'Photo');
    $formulaire->addElement('static', 'note'            , '', 'Image JPEG ou PNG de 90x120 pixels (sera redimensionnée si taille différente)');
}

for ($i = 1;$i < 4; $i++) {
    $formulaire->addElement('header', null, 'Présentation ' . $i);

    $formulaire->addElement('text', 'pres' . $i . '_titre', 'Titre', array('size' => 40, 'maxlength' => 80, 'class' => 'span7'));
    $formulaire->addElement('textarea', 'pres' . $i . '_abstract', 'Résumé', array('cols' => 60, 'rows' => 10, 'class' => 'span7'));

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Fonctionnel', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Technique'    , 2);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, 'Les deux'             , 3);
    $formulaire->addGroup($groupe, 'groupe_pres' . $i, "Public visé", '<br />', false);

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, 'Conférence plénière', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, 'Atelier'    , 2);
    $formulaire->addGroup($groupe, 'groupe_type_pres' . $i, "Type de session", '<br />', false);

    $formulaire->setDefaults(array('pres' . $i . '_journee' => 1,
                                   'pres' . $i . '_genre' => 1));
}

$formulaire->addElement('submit', 'soumettre', 'Soumettre', array('class' => 'btn primary', 'style' => 'float: right'));

$formulaire->addGroupRule('groupe_pres1', 'Selectionnez le public visé' , 'required', null, 1);
$formulaire->addRule('pres1_titre', 'Titre manquant', 'required');
$formulaire->addRule('pres1_abstract', 'Résumé manquant', 'required');

$formulaire->addGroupRule('groupe_type_pres1', 'Indiquez le type de session' , 'required', null, 1);
$formulaire->addRule('pres1_genre', 'Type de session manquant', 'required');

$formulaire->addRule('civilite1'  , 'Civilité non sélectionnée', 'required');
$formulaire->addRule('nom1'       , 'Nom manquant'             , 'required');
$formulaire->addRule('prenom1'    , 'Prénom manquant'          , 'required');
$formulaire->addRule('email1'     , 'Email manquant'           , 'required');
$formulaire->addRule('email1'     , 'Email invalide'           , 'email');
$formulaire->addRule('biographie1', 'Biographie manquante'     , 'required');
$formulaire->addRule('logo1'      , 'Photo manquante'          , 'uploadedfile');
$formulaire->addRule('logo1'      , 'Type d\'image incorrect (PNG ou JPEG)'  , 'mimetype' , array('image/png', 'image/jpeg'));

if ($formulaire->validate()) {
    $valeurs = $formulaire->exportValues();
    $forum = $forums->obtenir($valeurs['id_forum']);

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
        $file = $formulaire->getElement('logo'.$i);
        $data = $file->getValue();
        if ($data['name']) {
            $imageDir = realpath('../../templates/'.$forum['path'].'/images/intervenants/');
            // Transformation en 90x120 JPG pour simplifier
            $data = $file->getValue();
            if ($data['type'] == 'image/png') {
                $img = imagecreatefrompng($data['tmp_name']);
            } else {
                $img = imagecreatefromjpeg($data['tmp_name']);
            }
            $width = imagesx($img);
            $height = imagesy($img);
            if ($width != 90 || $height != 120) {
                $oldImg = $img;
                $img = imagecreatetruecolor(90, 120);
                imagecopyresampled($img, $oldImg, 0, 0, 0, 0, 90, 120, $width, $height);
            }
            imagejpeg($img, $imageDir . '/' . $$var . '.jpg', 90);
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
