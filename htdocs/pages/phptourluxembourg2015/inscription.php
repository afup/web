<?php

require_once dirname(__FILE__) . '/../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';

$pays = new AFUP_Pays($bdd);
$forum = new AFUP_Forum($bdd);
$id_forum = $config_forum['id'];
$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$forum_facturation = new AFUP_Facturation_Forum($bdd);

$nombre_places   = $forum->obtenirNombrePlaces($id_forum);
$nombre_inscrits = $forum_inscriptions->obtenirNombreInscrits($id_forum);

if (!isset($_GET['passage_en_force'])) {
	if (time() > $config_forum['date_fin_vente']) {
	  $smarty->display('inscriptions_fermes.html');
	  die();
	}
	$is_prevente = time() < $config_forum['date_fin_prevente'];
	if ($nombre_inscrits >= $nombre_places) {
	  $smarty->display('inscriptions_fermes.html');
	  die();
	}
}

if (time() > $config_forum['date_debut']) {
	$smarty->display('inscriptions_fermes.html');
	die();
}

//nombre possible d'inscrptions sur une même commande
$nombre_personnes = isset($_GET['nombre_personnes']) ? (int)$_GET['nombre_personnes'] : 5;
$nombre_tags = 3;

//nombre inscription choisi via js
$nombre_inscriptions = isset($_GET['nbInscriptions']) ? (int)$_GET['nbInscriptions'] : 1;
$smarty->assign('nbInscriptions', $nombre_inscriptions);

// On créé le formulaire
$formulaire = &instancierFormulaire();
$formulaire->setDefaults(array('civilite'            => 'M.',
                               'id_pays_facturation' => 'FR',
                               'type_inscription'    => -1,
                               'type_reglement'      => -1));

for ($i=1; $i <= $nombre_personnes; $i++) {
  $next = $i + 1;
  $formulaire->addElement('header'  , ''                       , '<a name="inscription'.$i.'">Personne</a> '.$i);
  $formulaire->addElement('select', 'civilite'.$i                 , 'Civilité'       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
  $formulaire->addElement('text'  , 'nom'.$i                      , 'Nom'            , array('size' => 30, 'maxlength' => 40));
  $formulaire->addElement('text'  , 'prenom'.$i                   , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
  $formulaire->addElement('text'  , 'email'.$i                    , 'Email'          , array('size' => 30, 'maxlength' => 100));
  $formulaire->addElement('text'  , 'telephone'.$i                , 'Tél.'           , array('size' => 20, 'maxlength' => 20));
  $groupe = array();
  if ($is_prevente) {
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, '2 jours en prévente : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_2_JOURNEES_PREVENTE] . ' € </strong> au lieu de '.$AFUP_Tarifs_Forum[AFUP_FORUM_2_JOURNEES].' €' , AFUP_FORUM_2_JOURNEES_PREVENTE);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, '2 jours membre AFUP : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE] . ' € </strong>', AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, '2 jours étudiant : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE] .  ' € </strong>', AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, 'Journée du 12 mai 2015 : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_PREMIERE_JOURNEE] . ' ' . EURO . '</strong>', AFUP_FORUM_PREMIERE_JOURNEE);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, 'Journée du 13 mai 2015 : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_PREMIERE_JOURNEE] . ' ' . EURO . '</strong>' , AFUP_FORUM_DEUXIEME_JOURNEE);
  } else {
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, '2 jours : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_2_JOURNEES] . ' ' . EURO . '</strong>'                                         , AFUP_FORUM_2_JOURNEES);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, '2 jours membre AFUP : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_2_JOURNEES_AFUP] . ' ' . EURO . '</strong>'                      , AFUP_FORUM_2_JOURNEES_AFUP);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, '2 jours étudiant * : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_2_JOURNEES_ETUDIANT] . ' ' . EURO . '</strong>'                     , AFUP_FORUM_2_JOURNEES_ETUDIANT);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, '2 jours avec coupon de réduction : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_2_JOURNEES_COUPON] . ' ' . EURO . '</strong>'                     , AFUP_FORUM_2_JOURNEES_COUPON);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, 'Journée du 12 mai 2015 : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_PREMIERE_JOURNEE] . ' ' . EURO . '</strong>', AFUP_FORUM_PREMIERE_JOURNEE);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription'.$i, null, 'Journée du 13 mai 2015 : <strong>' . $AFUP_Tarifs_Forum[AFUP_FORUM_PREMIERE_JOURNEE] . ' ' . EURO . '</strong>' , AFUP_FORUM_DEUXIEME_JOURNEE);
  }

  $formulaire->addGroup($groupe, 'groupe_type_inscription'.$i, 'Formule', '<br />', false);
  $formulaire->addElement('static'  , 'raccourci'                   , ''               , 'Merci de renseigner 3 tags ( mot clef) vous caractérisant, ces tags seront imprimés sur votre badge afin de faciliter le networking pendant le PHP Tour Luxembourg');
  for ($j=1; $j <= $nombre_tags; $j++) {
    $formulaire->addElement('text'  , 'tag_'.$j.'_'.$i                      , 'Tag '.$j            , array('size' => 30, 'maxlength' => 40));

  }
    $formulaire->addElement('static'  , 'raccourci' , '' , '<i>Ex : framework, hosting,  gestion de projet, Symfony, Zend Framework, Test unitaire.....</i>');
  if ($i == $nombre_personnes) {
    $formulaire->addElement('static'  , 'raccourci'                   , ''               , '<a href="#facturation" class="double">passer à la facturation</a>.');
  } else {
    $formulaire->addElement('static'  , 'raccourci'                   , ''               , '<a href="#inscription'.$next.'">Ajouter une autre inscription</a><br />ou <a href="#facturation" class="double">passer à la facturation</a>.');
  }
}

$formulaire->addElement('header'  , ''                       , '<a name="facturation">Facturation</a>');
$groupe = array();
$groupe[] = &HTML_QuickForm::createElement('radio', 'type_reglement', null, 'Carte bancaire', AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE);
$groupe[] = &HTML_QuickForm::createElement('radio', 'type_reglement', null, 'Chèque'        , AFUP_FORUM_REGLEMENT_CHEQUE);
$formulaire->addGroup($groupe, 'groupe_type_reglement', 'Règlement', '&nbsp;', false);
$formulaire->addElement('static'  , 'note'                   , ''               , 'Ces informations concernent la personne ou la société qui sera facturée<br /><br />');
$formulaire->addElement('text'    , 'societe_facturation'    , 'Société'        , array('size' => 50, 'maxlength' => 100));
$formulaire->addElement('text'    , 'nom_facturation'        , 'Nom'            , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'    , 'prenom_facturation'     , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('textarea', 'adresse_facturation'    , 'Adresse'        , array('cols' => 42, 'rows'      => 10));
$formulaire->addElement('text'    , 'code_postal_facturation', 'Code postal'    , array('size' =>  6, 'maxlength' => 10));
$formulaire->addElement('text'    , 'ville_facturation'      , 'Ville'          , array('size' => 30, 'maxlength' => 50));
$formulaire->addElement('select'  , 'id_pays_facturation'    , 'Pays'           , $pays->obtenirPays());
$formulaire->addElement('text'    , 'email_facturation'      , 'Email (facture)', array('size' => 30, 'maxlength' => 100));
$formulaire->addElement('text'    , 'coupon'                 , 'Coupon'         , array('size' => 30, 'maxlength' => 200));

$formulaire->addElement('header', null, 'Divers');
$formulaire->addElement('static', null, null, "J'accepte que ma compagnie soit citée comme participant à la conférence");
$groupe = array();
$groupe[] = &HTML_QuickForm::createElement('radio', 'citer_societe', null, 'oui', 1);
$groupe[] = &HTML_QuickForm::createElement('radio', 'citer_societe', null, 'non', 0);
$formulaire->addGroup($groupe, 'groupe_citer_societe', null, '&nbsp;', false);
$formulaire->addElement('static', null, null, "Je souhaite être tenu au courant des rencontres de l'AFUP sur des sujets afférents à PHP");
$groupe = array();
$groupe[] = &HTML_QuickForm::createElement('radio', 'newsletter_afup', null, 'oui', 1);
$groupe[] = &HTML_QuickForm::createElement('radio', 'newsletter_afup', null, 'non', 0);
$formulaire->addGroup($groupe, 'groupe_newsletter_afup', null, '&nbsp;', false);
$formulaire->addElement('static', null, null, "Je souhaite être tenu au courant de l'actualité PHP via la newsletter de notre sponsor");
$groupe = array();
$groupe[] = &HTML_QuickForm::createElement('radio', 'newsletter_nexen', null, 'oui', 1);
$groupe[] = &HTML_QuickForm::createElement('radio', 'newsletter_nexen', null, 'non', 0);
$formulaire->addGroup($groupe, 'groupe_newsletter_nexen', null, '&nbsp;', false);

$formulaire->addElement('header', 'boutons'  , '');
$formulaire->addElement('submit', 'soumettre', 'Soumettre');

$formulaire->addGroupRule('groupe_type_inscription1', 'Formule non sélectionnée' , 'required', null, 1);
$formulaire->addGroupRule('groupe_type_reglement'  , 'Règlement non sélectionné', 'required', null, 1);
$formulaire->addRule('civilite1'               , 'Civilité non sélectionnée', 'required');
$formulaire->addRule('nom1'                    , 'Nom manquant'             , 'required');
$formulaire->addRule('prenom1'                 , 'Prénom manquant'          , 'required');
$formulaire->addRule('email1'                  , 'Email manquant'           , 'required');
$formulaire->addRule('email1'                  , 'Email invalide'           , 'email');

for ($i=2; $i <= $nombre_personnes; $i++) {
  if ((isset($_POST['nom'.$i]) && $_POST['nom'.$i] != '') || (isset($_POST['prenom'.$i]) && $_POST['prenom'.$i] != '') || (isset($_POST['email'.$i])&& $_POST['email'.$i] != '')) {
    $formulaire->addRule('nom'.$i                    , 'Nom manquant'             , 'required');
    $formulaire->addRule('prenom'.$i                 , 'Prénom manquant'          , 'required');
    $formulaire->addRule('email'.$i                  , 'Email manquant'           , 'required');
    $formulaire->addRule('email'.$i                  , 'Email invalide'           , 'email');
    $formulaire->addGroupRule('groupe_type_inscription'.$i, 'Formule non sélectionnée' , 'required', null, 1);
  }
}

$formulaire->addRule('adresse_facturation'    , 'Adresse manquante'                        , 'required');
$formulaire->addRule('code_postal_facturation', 'Code postal manquant'                     , 'required');
$formulaire->addRule('ville_facturation'      , 'Ville manquante'                          , 'required');
$formulaire->addRule('id_pays_facturation'    , 'Pays non sélectionné'                     , 'required');
$formulaire->addRule('email_facturation'      , 'Email de réception de la facture manquant', 'required');
$formulaire->addRule('coupon'                 , 'Coupon non valable'                       , 'regex'   , '/^(|'.implode($config_forum['coupons'],'|').')$/');

if ($formulaire->validate()) {
  $valeurs = $formulaire->exportValues();

  $valeurs['id_forum'] = $id_forum;

  if (!isset($valeurs['nom'])) {
    $valeurs['nom'] = 'Anonyme';
  }
  $label = (empty($valeurs['societe_facturation']) ? (empty($valeurs['nom_facturation']) ? $valeurs['nom'] : $valeurs['nom_facturation']) : $valeurs['societe_facturation']);

  $probleme = 0;
  if (preg_match("/<a href=/", $valeurs['adresse_facturation'])) {
    $probleme = 1;
  }

  if (!$probleme) {
    $valeurs['reference'] = $forum_facturation->creerReference($valeurs['id_forum'], $label);

    // On ajoute l'inscription dans la base de données
    // TODO : Gérer cela correctement
    $total = 0;
    for ($i=1; $i<=$nombre_personnes; $i++) {
      $ok = 1;
      if ($valeurs['nom'.$i] != '') {
        $tags = '';
        for ($j=1; $j <= $nombre_tags; $j++) {
          $tags .=';'. $valeurs['tag_'.$j.'_'.$i];
          //$formulaire->addElement('text'  , 'tag_'.$j.'_'.$i                      , 'Tag '.$j            , array('size' => 30, 'maxlength' => 40));

        }
        $ok = $forum_inscriptions->ajouterInscription($valeurs['id_forum'],
        $valeurs['reference'],
        $valeurs['type_inscription'.$i],
        $valeurs['civilite'.$i],
        $valeurs['nom'.$i],
        $valeurs['prenom'.$i],
        $valeurs['email'.$i],
        $valeurs['telephone'.$i],
        $valeurs['coupon'],
        $valeurs['citer_societe'],
        $valeurs['newsletter_afup'],
        $valeurs['newsletter_nexen'],
        '<tag>'.$tags.'</tags>');
        $total += $AFUP_Tarifs_Forum[$valeurs['type_inscription'.$i]];
      }
      if (!$ok) {
        $probleme = 1;
      }
    }

    if ($ok) {
      $probleme = !$forum_facturation->gererFacturation($valeurs['reference'],
      $valeurs['type_reglement'],
      null,
      null,
      $valeurs['email_facturation'],
      $valeurs['societe_facturation'],
      $valeurs['nom_facturation'],
      $valeurs['prenom_facturation'],
      $valeurs['adresse_facturation'],
      $valeurs['code_postal_facturation'],
      $valeurs['ville_facturation'],
      $valeurs['id_pays_facturation'],
      $valeurs['id_forum'],
      null);
    }
  }

  if ($probleme == 1) {
    $smarty->assign('erreur', 'Une erreur est survenue lors de votre inscription.<br />Veuillez contacter le responsable des inscriptions afin de régler le problème.');
  } else {
    if ($valeurs['type_reglement'] == AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE) {
      require_once dirname(__FILE__).'/../../../dependencies/paybox/payboxv2.inc';
      $paybox = new PAYBOX;
      $paybox->set_langue('FRA');
      $paybox->set_site($conf->obtenir('paybox|site'));
      $paybox->set_rang($conf->obtenir('paybox|rang'));
      $paybox->set_identifiant('83166771');

      $paybox->set_total($total * 100);
      $paybox->set_cmd($valeurs['reference']);
      $paybox->set_porteur($valeurs['email_facturation']);

      $paybox->set_effectue('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_effectue.php');
      $paybox->set_refuse('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_refuse.php');
      $paybox->set_annule('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_annule.php');
      $paybox->set_erreur('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_erreur.php');

      $paybox->set_wait(50000);
      $paybox->set_boutpi('Régler par carte');
      $paybox->set_bkgd('#FAEBD7');
      $paybox->set_output('B');

      preg_match('#<CENTER>(.*)</CENTER>#is', $paybox->paiement(), $r);
      $smarty->assign('paybox', $r[1]);
      $smarty->display('paybox_formulaire.html');
    } else {
      $smarty->display('inscription_paiement.html');
    }
    die();
  }
}

$smarty->assign('formulaire', genererFormulaire($formulaire));
$smarty->display($is_prevente?'inscription_prevente.html':'inscription.html');
