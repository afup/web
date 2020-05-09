<?php

use Afup\Site\Forum\Inscriptions;
use Afup\Site\Utils\Pays;

ini_set('display_errors',TRUE);
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';


$pays = new Pays($bdd);

$inscriptions = new Inscriptions($bdd);

// S'il ne reste plus de place, on ne permet pas l'inscription
$nombre_places   = 200; // TODO : Mettre cela dans le fichier de configuration

//nombre possible d'inscrptions sur une même commande
$nombre_personnes = 5;


// On créé le formulaire
$formulaire = instancierFormulaire();
$formulaire->setDefaults(array('civilite'            => 'M.',
                               'id_pays_facturation' => 'FR',
                               'type_inscription'    => -1,
                               'type_reglement'      => -1));

for ($i=1;$i <= $nombre_personnes;$i++)
{
	
	$next = $i + 1;
	$formulaire->addElement('header'  , ''                       , '<a name="inscription'.$i.'">Personne</a> '.$i);
	$formulaire->addElement('select', 'civilite'.$i                 , 'Civilité'       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
	$formulaire->addElement('text'  , 'nom'.$i                      , 'Nom'            , array('size' => 30, 'maxlength' => 40));
	$formulaire->addElement('text'  , 'prenom'.$i                   , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
	$formulaire->addElement('text'  , 'email'.$i                    , 'Email'          , array('size' => 30, 'maxlength' => 100));
	$formulaire->addElement('text'  , 'telephone'.$i                , 'Tél.'           , array('size' => 20, 'maxlength' => 20));
	$groupe = array();
	$groupe[] = $formulaire->createElement('radio', 'type_inscription'.$i, null, 'Journée fonctionnelle, jeudi 09 novembre 2006 : <strong>100,00 ' . EURO . '</strong>', AFUP_FORUM_PREMIERE_JOURNEE);
	$groupe[] = $formulaire->createElement('radio', 'type_inscription'.$i, null, 'Journée technique, vendredi 10 novembre 2006 : <strong>100,00 ' . EURO . '</strong>'       , AFUP_FORUM_DEUXIEME_JOURNEE);
	$groupe[] = $formulaire->createElement('radio', 'type_inscription'.$i, null, 'Deux jours : <strong>150,00 ' . EURO . '</strong>'                                      , AFUP_FORUM_2_JOURNEES);
	$groupe[] = $formulaire->createElement('radio', 'type_inscription'.$i, null, 'Deux jours (membre AFUP) : <strong>100,00 ' . EURO . '</strong>'                        , AFUP_FORUM_2_JOURNEES_AFUP);
	$groupe[] = $formulaire->createElement('radio', 'type_inscription'.$i, null, 'Deux jours (étudiant) : <strong>100,00 ' . EURO . '</strong>'                           , AFUP_FORUM_2_JOURNEES_ETUDIANT);
	$formulaire->addGroup($groupe, 'groupe_type_inscription'.$i, 'Formule', '<br />', false);
	if ($i == $nombre_personnes)
		$formulaire->addElement('static'  , 'raccourci'                   , ''               , '<a href="#facturation" class="double">passer à la facturation</a>.');
	else
		$formulaire->addElement('static'  , 'raccourci'                   , ''               , '<a href="#inscription'.$next.'">Ajouter une autre inscription</a><br />ou <a href="#facturation" class="double">passer à la facturation</a>.');	
}

$formulaire->addElement('header'  , ''                       , '<a name="facturation">Facturation</a>');
$groupe = array();
$groupe[] = $formulaire->createElement('radio', 'type_reglement', null, 'Carte bancaire', AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE);
$groupe[] = $formulaire->createElement('radio', 'type_reglement', null, 'Chèque'        , AFUP_FORUM_REGLEMENT_CHEQUE);
$formulaire->addGroup($groupe, 'groupe_type_reglement', 'Règlement', '&nbsp;', false);
$formulaire->addElement('static'  , 'note'                   , ''               , 'Ces informations concernent la personne ou la société qui sera facturée<br /><br />');
$formulaire->addElement('text'    , 'societe_facturation'    , 'Société'        , array('size' => 50, 'maxlength' => 100));
$formulaire->addElement('text'    , 'nom_facturation'        , 'Nom'            , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'    , 'prenom_facturation'     , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('textarea', 'adresse_facturation'    , 'Adresse'        , array('cols' => 42, 'rows'      => 10));
$formulaire->addElement('text'    , 'code_postal_facturation', 'Code postal'    , array('size' =>  6, 'maxlength' => 10));
$formulaire->addElement('text'    , 'ville_facturation'      , 'Ville'          , array('size' => 30, 'maxlength' => 50));
$formulaire->addElement('select'  , 'id_pays_facturation'    , 'Pays'           , $pays->obtenirPays());

$formulaire->addElement('header', null, 'Divers');
$formulaire->addElement('static', null, null, "J'accepte que ma compagnie soit citée comme participant à la conférence");
$groupe = array();
$groupe[] = $formulaire->createElement('radio', 'citer_societe', null, 'oui', 1);
$groupe[] = $formulaire->createElement('radio', 'citer_societe', null, 'non', 0);
$formulaire->addGroup($groupe, 'groupe_citer_societe', null, '&nbsp;', false);
$formulaire->addElement('static', null, null, "Je souhaite être tenu au courant des rencontres de l'AFUP sur des sujets afférents à PHP");
$groupe = array();
$groupe[] = $formulaire->createElement('radio', 'newsletter_afup', null, 'oui', 1);
$groupe[] = $formulaire->createElement('radio', 'newsletter_afup', null, 'non', 0);
$formulaire->addGroup($groupe, 'groupe_newsletter_afup', null, '&nbsp;', false);
$formulaire->addElement('static', null, null, "Je souhaite être tenu au courant de l'actualité PHP via la newsletter de notre sponsor");
$groupe = array();
$groupe[] = $formulaire->createElement('radio', 'newsletter_nexen', null, 'oui', 1);
$groupe[] = $formulaire->createElement('radio', 'newsletter_nexen', null, 'non', 0);
$formulaire->addGroup($groupe, 'groupe_newsletter_nexen', null, '&nbsp;', false);

$formulaire->addElement('header', 'boutons'  , '');
$formulaire->addElement('submit', 'soumettre', 'Soumettre');

// On ajoute les règles
$formulaire->addGroupRule('groupe_type_inscription1', 'Formule non sélectionnée' , 'required', null, 1);
$formulaire->addGroupRule('groupe_type_reglement'  , 'Règlement non sélectionné', 'required', null, 1);
$formulaire->addRule('civilite1'               , 'Civilité non sélectionnée', 'required');
$formulaire->addRule('nom1'                    , 'Nom manquant'             , 'required');
$formulaire->addRule('prenom1'                 , 'Prénom manquant'          , 'required');
$formulaire->addRule('email1'                  , 'Email manquant'           , 'required');
$formulaire->addRule('email1'                  , 'Email invalide'           , 'email');

//si un element des chaque personne > 1 est rempli, on teste les autres champs
for ($i=2;$i<=$nombre_personnes;$i++)
{
	if ((isset($_POST['nom'.$i]) && $_POST['nom'.$i] != '') || (isset($_POST['prenom'.$i]) && $_POST['prenom'.$i] != '') || (isset($_POST['email'.$i])&& $_POST['email'.$i] != ''))
	{
		$formulaire->addRule('nom'.$i                    , 'Nom manquant'             , 'required');
		$formulaire->addRule('prenom'.$i                 , 'Prénom manquant'          , 'required');
		$formulaire->addRule('email'.$i                  , 'Email manquant'           , 'required');
		$formulaire->addRule('email'.$i                  , 'Email invalide'           , 'email');
		$formulaire->addGroupRule('groupe_type_inscription'.$i, 'Formule non sélectionnée' , 'required', null, 1);		
	}
}

$formulaire->addRule('adresse_facturation'    , 'Adresse manquante'        , 'required');
$formulaire->addRule('code_postal_facturation', 'Code postal manquant'     , 'required');
$formulaire->addRule('ville_facturation'      , 'Ville manquante'          , 'required');
$formulaire->addRule('id_pays_facturation'    , 'Pays non sélectionné'     , 'required');

// On traite le formulaire une fois validé
if ($formulaire->validate()) {
    $valeurs = $formulaire->exportValues();

    // On génére la référence
    $reference = strtoupper('F' . date('Y') . '-' . date('dmY') . '-' . substr($valeurs['nom'], 0, 5) . '-' . substr(md5(date('r') . $valeurs['nom']), -3));
    $reference = supprimerAccents($reference);
    $reference = preg_replace('/[^A-Z0-9_\-\:\.;]/', '', $reference);

    // On ajoute l'inscription dans la base de données
    // TODO : Gérer cela correctement
    $total = 0;
    $tarifs = array(AFUP_FORUM_PREMIERE_JOURNEE => 100,
                            AFUP_FORUM_DEUXIEME_JOURNEE => 100,
                            AFUP_FORUM_2_JOURNEES       => 150,
                            AFUP_FORUM_2_JOURNEES_AFUP  => 100,
							AFUP_FORUM_2_JOURNEES_ETUDIANT => 100);
    for ($i=1;$i<=$nombre_personnes;$i++)
    {
    	$ok = 1;
    	if ($valeurs['nom'.$i] != '')
    	{	
    		$ok = $inscriptions->ajouterInscription($reference,
                                            $valeurs['type_inscription'.$i],
                                            $valeurs['type_reglement'],
                                            null,
                                            $valeurs['civilite'.$i],
                                            $valeurs['nom'.$i],
                                            $valeurs['prenom'.$i],
                                            $valeurs['email'.$i],
                                            $valeurs['telephone'.$i],
                                            $valeurs['societe_facturation'],
                                            $valeurs['nom_facturation'],
                                            $valeurs['prenom_facturation'],
                                            $valeurs['adresse_facturation'],
                                            $valeurs['code_postal_facturation'],
                                            $valeurs['ville_facturation'],
                                            $valeurs['id_pays_facturation'],
                                            $valeurs['citer_societe'],
                                            $valeurs['newsletter_afup'],
                                            $valeurs['newsletter_nexen']);
     		$total += $tarifs[$valeurs['type_inscription'.$i]];                                       
    	}
    	if (!$ok)
    	{
    		$probleme = 1;
    	}
    }
    if ($probleme == 1) {
        $smarty->assign('erreur', 'Une erreur est survenue lors de votre inscription.<br />Veuillez contacter le responsable des inscriptions afin de régler le problème.');
    } else {
        if ($valeurs['type_reglement'] == AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE) {
            // On passe la main à Paybox pour la suite
            require_once 'paybox/payboxv2.inc';
            $paybox = new PAYBOX;
            $paybox->set_langue('FRA'); // Langue de l'interface PayBox
            $paybox->set_site('8316677'); // TODO : Mettre dans le fichier de configuration
            //$paybox->set_site('1999888'); // TEST
            $paybox->set_rang('01'); // TODO : Mettre dans le fichier de configuration
            // $paybox->set_rang('99'); // TEST
            $paybox->set_identifiant('83166771'); 
            
            $paybox->set_total($total * 100); // Total de la commande, en centimes d'euros
            $paybox->set_cmd($reference); // Référence de la commande
            $paybox->set_porteur($valeurs['email1']); // Email du client final (Le porteur de la carte)

            // URL en cas de reussite
            $paybox->set_effectue('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_effectue.php');
            // URL en cas de refus du paiement
            $paybox->set_refuse('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_refuse.php');
            // URL en cas d'annulation du paiement de la part du client
            $paybox->set_annule('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_annule.php');
            // URL en cas de disfonctionnement de PayBox
            $paybox->set_erreur('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_erreur.php');

            $paybox->set_wait(50000); // Délai d'attente avant la redirection
            $paybox->set_boutpi('Régler par carte'); // Texte du bouton
            $paybox->set_bkgd('#FAEBD7'); // Fond de page
            $paybox->set_output('B'); // On veut gerer l'affichage dans la page intermediaire
            preg_match('#<CENTER>(.*)</CENTER>#is', $paybox->paiement(), $r);
            $smarty->assign('paybox', $r[1]);
            $smarty->display('paybox_formulaire.html');
        } else {
            $smarty->display('inscription_paiement.html');
        }
        die();
    }
}

// On affiche la page
$smarty->assign('formulaire', genererFormulaire($formulaire));
$smarty->display('inscription.html');
?>
