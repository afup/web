<?php

// Impossible to access the file itself
use Afup\Site\Planete\Flux;
use Afup\Site\Utils\Logs;
use AppBundle\Association\Model\Repository\UserRepository;

/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$userRepository = $this->get(UserRepository::class);

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer', 'tester'));
$tris_valides = array('nom', 'url', 'etat');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);



$planete_flux = new Flux($bdd);

if ($action == 'lister') {

    // Valeurs par défaut des paramètres de tri
    $list_champs = '*';
    $list_ordre = 'nom';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramètres de tri en fonction des demandes passées en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }
    
    $flux = $planete_flux->obtenirListe($list_champs, $list_ordre, $list_associatif, $list_filtre);
    if (isset($_GET['testerFlux']) && $_GET['testerFlux'] == 1) {
        ini_set('display_errors', 0); //on n'affiche rien du tout
        set_time_limit(240);
    }
    foreach ($flux as &$f) {
        if (isset($_GET['testerFlux']) && $_GET['testerFlux'] == 1) {
            if ($f['etat']) {
                $content = file_get_contents($f['feed']);
                try {
                    $rss = new SimpleXmlElement($content);
                    $f['result'] = 'green';
                } catch(Exception $e){
                    $f['result'] = 'red';
                }
            }
        } else {
            $f['result'] = 'blue';
        }
    }
    // Mise en place de la liste dans le scope de smarty
    $smarty->assign('flux', $flux);
} elseif ($action == 'supprimer') {
    if ($planete_flux->supprimer($_GET['id'])) {
        Logs::log('Suppression du flux ' . $_GET['id']);
        afficherMessage('Le flux a été supprimé', 'index.php?page=planete_flux&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression du flux', 'index.php?page=planete_flux&action=lister', true);
    }
} else {
    $formulaire = instancierFormulaire();
    if ($action == 'ajouter') {
        $formulaire->setDefaults(array('url' => 'http://',
                                       'feed' => 'http://',
                                       'etat'    => AFUP_DROITS_ETAT_ACTIF));    
    } else {
        $champs = $planete_flux->obtenir($_GET['id']);
        $formulaire->setDefaults($champs);    
    }
    $users = [null => ''];
    foreach ($userRepository->search() as $user) {
        $users[$user->getId()] = $user->getLastName().' '.$user->getFirstName();
    }
    $formulaire->addElement('header'  , ''                     , 'Informations');
    $formulaire->addElement('text'    , 'nom'                  , 'Nom'            , array('size' => 30, 'maxlength' => 40));
    $formulaire->addElement('text'    , 'url'                  , 'URL'            , array('size' => 50, 'maxlength' => 200));
    $formulaire->addElement('text'    , 'feed'                 , 'Flux'           , array('size' => 50, 'maxlength' => 200));
    $formulaire->addElement('select'  , 'id_personne_physique' , 'Personne physique', $users);
    
    $formulaire->addElement('header'  , ''                     , 'Paramètres');
    $formulaire->addElement('select'  , 'etat'                 , 'Etat'        , array(AFUP_DROITS_ETAT_ACTIF   => 'Actif',
                                                                                   AFUP_DROITS_ETAT_INACTIF => 'Inactif'));
    
    $formulaire->addElement('header'  , 'boutons'              , '');
    $formulaire->addElement('submit'  , 'soumettre'            , ucfirst($action));
    
    $formulaire->addRule('nom'         , 'Nom manquant'           , 'required');
    $formulaire->addRule('url'         , 'URL manquante manquant' , 'required');
    $formulaire->addRule('feed'        , 'Flux manquant'          , 'required');
    
    if ($formulaire->validate()) {
        if ($action == 'ajouter') {
            $ok = $planete_flux->ajouter($formulaire->exportValue('nom'),
                                              $formulaire->exportValue('url'),
                                              $formulaire->exportValue('feed'),
                                              $formulaire->exportValue('etat'),
                                              $formulaire->exportValue('id_personne_physique'));
        } else {
            $ok = $planete_flux->modifier($_GET['id'],
                                               $formulaire->exportValue('nom'),
                                               $formulaire->exportValue('url'),
                                               $formulaire->exportValue('feed'),
                                               $formulaire->exportValue('etat'),
                                               $formulaire->exportValue('id_personne_physique'));
        }
        
        if ($ok) {
            if ($action == 'ajouter') {
                Logs::log('Ajout du flux ' . $formulaire->exportValue('nom'));
            } else {
                Logs::log('Modification du flux ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
            }            
            afficherMessage('Le flux a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=planete_flux&action=lister');    
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' du flux');    
        }    
    } 
    
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
