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

$action = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

$formulaire = &instancierFormulaire($action);
$formulaire->setDefaults(array('civilite' => $translator->trans('M.'),));

$formulaire->addElement('hidden', 'id_forum', $config_forum['id']);

//$formulaire->addElement('header', null, 'Coordonnées des conférenciers (deux au maximum)');
$groupe = array();

for ($i = 1; $i < 3; $i++) {
    $html = $i == 1?'':' ('. $translator->trans('optionnel') . ')';
    $formulaire->addElement('header', null, $translator->trans('Conférencier') . ' #' .$i.$html);
    $formulaire->addElement('select', 'civilite' . $i   , $translator->trans('Civilité')       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
    $formulaire->addElement('text'  , 'nom' . $i        , $translator->trans('Nom')            , array('size' => 30, 'maxlength' => 40, 'class' => 'span7'));
    $formulaire->addElement('text'  , 'prenom' . $i     , $translator->trans('Prénom')         , array('size' => 30, 'maxlength' => 40, 'class' => 'span7'));
    $formulaire->addElement('text'  , 'email' . $i      , $translator->trans('Email')          , array('size' => 30, 'maxlength' => 100, 'class' => 'span7'));
    $formulaire->addElement('text'  , 'telephone' . $i  , $translator->trans('Tél.')           , array('size' => 20, 'maxlength' => 20, 'class' => 'span7'));
    $formulaire->addElement('text'  , 'societe' . $i    , $translator->trans('Société')        , array('size' => 50, 'maxlength' => 100, 'class' => 'span7'));
    $formulaire->addElement('textarea', 'biographie' . $i, $translator->trans('Biographie'), array('cols' => 60, 'rows' => 5, 'class' => 'span7'));
    $formulaire->addElement('file'  , 'logo' . $i       , $translator->trans('Photo'));
    $formulaire->addElement('static', 'note'            , '', $translator->trans('Image JPEG ou PNG de 90x120 pixels (sera redimensionnée si taille différente)'));
}

for ($i = 1;$i < 4; $i++) {
    $formulaire->addElement('header', null, $translator->trans('Présentation') . ' #' . $i);

    $formulaire->addElement('text', 'pres' . $i . '_titre', $translator->trans('Titre'), array('size' => 40, 'maxlength' => 80, 'class' => 'span7'));
    $formulaire->addElement('textarea', 'pres' . $i . '_abstract', $translator->trans('Résumé'), array('cols' => 60, 'rows' => 10, 'class' => 'span7'));

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, $translator->trans('Fonctionnel'), 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, $translator->trans('Technique')    , 2);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_journee', null, $translator->trans('Les deux')             , 3);
    $formulaire->addGroup($groupe, 'groupe_pres' . $i, $translator->trans("Public visé"), '<br />', false);

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, $translator->trans('Conférence plénière (40 min)'), 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, $translator->trans('Conférence plénière (20 min)'), 3);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'pres' . $i . '_genre', null, $translator->trans('Atelier')    , 2);
    $formulaire->addGroup($groupe, 'groupe_type_pres' . $i, $translator->trans("Type de session"), '<br />', false);

    $formulaire->setDefaults(array('pres' . $i . '_journee' => 1,
                                   'pres' . $i . '_genre' => 1));
}

$formulaire->addElement('submit', 'soumettre', $translator->trans('Soumettre'), array('class' => 'btn primary', 'style' => 'float: right'));

$formulaire->addGroupRule('groupe_pres1', $translator->trans('Selectionnez le public visé') , 'required', null, 1);
$formulaire->addRule('pres1_titre', $translator->trans('Titre manquant'), 'required');
$formulaire->addRule('pres1_abstract', $translator->trans('Résumé manquant'), 'required');

$formulaire->addGroupRule('groupe_type_pres1', $translator->trans('Indiquez le type de session') , 'required', null, 1);
$formulaire->addRule('pres1_genre', $translator->trans('Type de session manquant'), 'required');

$formulaire->addRule('civilite1'  , $translator->trans('Civilité non sélectionnée'), 'required');
$formulaire->addRule('nom1'       , $translator->trans('Nom manquant')             , 'required');
$formulaire->addRule('prenom1'    , $translator->trans('Prénom manquant')          , 'required');
$formulaire->addRule('email1'     , $translator->trans('Email manquant')           , 'required');
$formulaire->addRule('email1'     , $translator->trans('Email invalide')           , 'email');
$formulaire->addRule('biographie1', $translator->trans('Biographie manquante')     , 'required');
$formulaire->addRule('logo1'      , $translator->trans('Photo manquante')          , 'uploadedfile');
$formulaire->addRule('logo1'      , $translator->trans('Type d\'image incorrect (PNG ou JPEG)')  , 'mimetype' , array('image/png', 'image/jpeg'));

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
            $imageDir = realpath('../../templates/phptourclermont2016/images/intervenants/');
            // Transformation en 90x120 JPG pour simplifier
            $data = $file->getValue();
            if ($data['type'] == 'image/png') {
                $img = imagecreatefrompng($data['tmp_name']);
            } else {
                $img = imagecreatefromjpeg($data['tmp_name']);
            }
            $originalWidth = $width = imagesx($img);
            $originalHeight = $height = imagesy($img);
            if ($width > 90 || $height > 120) {
                $oldImg = $img;

                $ratio = $width / $height;

                /**
                 *  RATIO = LARGEUR / HAUTEUR
                 *
                 *  RATIO / LARGEUR = 1 / HAUTEUR ==> LARGEUR / RATIO = HAUTEUR
                 *  RATIO x HAUTEUR = LARGEUR
                 *
                 * **************************
                 * HAUTEUR = LARGEUR / RATIO
                 * LARGEUR = RATIO x HAUTEUR
                 * **************************
                 */


                if ($width > 90) {
                    $width = 90;
                    $height = (int) ($width / $ratio);
                }
                // If after that, height is still to high
                if ($height > 120) {
                    $height = 120;
                    $width = (int) ($ratio * $height);
                }

                $img = imagecreatetruecolor($width, $height);
                imagecopyresampled($img, $oldImg, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
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
            $smarty->assign('erreur', $translator->trans('Une erreur est survenue lors de votre inscription.<br />Veuillez contacter le responsable des inscriptions afin de régler le problème.'));
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
$formulaire->updateAttributes(array('enctype' => 'multipart/form-data-alternate')); // little hack to allow distant forward by php
$smarty->assign('formulaire', genererFormulaire($formulaire));
$smarty->display('appel-a-conferenciers.html');
?>
