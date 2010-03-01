<?php
$action = 'modifier';

require_once 'afup/AFUP_Assemblee_Generale.php';
$assemblee_generale = new AFUP_Assemblee_Generale($bdd);

$timestamp = $assemblee_generale->obternirDerniereDate();

if ($timestamp > strtotime("-1 day", time())) {
    $date_assemblee_generale = convertirTimestampEnDate($timestamp);
    list($presence, $id_personne_avec_pouvoir) = $assemblee_generale->obtenirInfos($_SESSION['afup_login'], $timestamp);
    $assemblee_generale->marquerConsultation($_SESSION['afup_login'], $timestamp);
    
    $smarty->assign('date_assemblee_generale', $date_assemblee_generale);
    
    $formulaire = &instancierFormulaire('index.php?page=membre_assemblee_generale');
    $formulaire->setDefaults(array('date' => date("d/m/Y", time()),
                                   'presence' => $presence,
                                   'id_personne_avec_pouvoir' => $id_personne_avec_pouvoir));    
    
    $formulaire->addElement('header'  , ''         , 'Je serais présent(e)');
    $formulaire->addElement('radio'   , 'presence' , 'Oui'                   , '', AFUP_ASSEMBLEE_GENERALE_PRESENCE_OUI);
    $formulaire->addElement('radio'   , 'presence' , 'Non'                   , '', AFUP_ASSEMBLEE_GENERALE_PRESENCE_NON);
    $formulaire->addElement('radio'   , 'presence' , 'Je ne sais pas encore' , '', AFUP_ASSEMBLEE_GENERALE_PRESENCE_INDETERMINE);
    
    $formulaire->addElement('header'  , ''                         , 'Je donne mon pouvoir à');
    $formulaire->addElement('select'  , 'id_personne_avec_pouvoir' , 'Nom' , array(null => '' ) + $assemblee_generale->obtenirPresents($timestamp));
    
    $formulaire->addElement('header'  , 'boutons'   , '');
    $formulaire->addElement('hidden'  , 'date'      , $timestamp);
    $formulaire->addElement('submit'  , 'soumettre' , 'confirmer');
    
    if ($formulaire->validate()) {
        if ($action == 'modifier') {
            $ok = $assemblee_generale->modifier($_SESSION['afup_login'],
                                                $timestamp,
                                                $formulaire->exportValue('presence'),
                                                $formulaire->exportValue('id_personne_avec_pouvoir'));
        }
        
        if ($ok) {
            if ($action == 'modifier') {
                AFUP_Logs::log('Modification de la présence et du pouvoir de la personne physique');
            }            
            afficherMessage('La présence et le pouvoir ont été modifiée', 'index.php?page=membre_assemblee_generale');    
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de la modification de la prsence et du pouvoir');    
        }    
    } 
    
    $smarty->assign('formulaire', genererFormulaire($formulaire));
} else {
		unset($assemblee_generale);
}
?>