<?php
require_once dirname(__FILE__).'/AFUP_Configuration.php';
require_once dirname(__FILE__).'/AFUP_Base_De_Donnees.php';

class AFUP_Web {
    function mettreAJour($update = false) {
    	if ($update === true) {
            // Direction le dossier avec le repo git
            $command = "cd ".$GLOBALS['conf']->obtenir('git|local_repo').";";
            // Si modif local, on la stocke temporairement pour éviter les conflits
            $command .= "git stash;";
            // On récupère les éventuelles nouvelles branches distantes
            $command .= "git fetch";
            // On track la branche souhaitée dans une branche locale (même si déjà fait)
            $command .= "git branch --track branch-name origin/".$GLOBALS['conf']->obtenir('git|deployed_branch')." 2> /dev/null;";
            // On se déplace sur la branche souhaitée
            $command .= "git checkout ".$GLOBALS['conf']->obtenir('git|deployed_branch').";";
            // On la met à jour
            $command .= "git pull origin;";
            // On réapplique les modifs locales
            $command .= "git stash pop;";
            // On extrait la branche vers le dossier d'export en ajoutant et en écrasant les fichiers
            $command .= "git checkout-index -f -a --prefix=/".$GLOBALS['conf']->obtenir('git|local_export') . " ; ";
            // Direction le dossier exporté
            $command .= "cd /".$GLOBALS['conf']->obtenir('git|local_export').";";
            // Nettoyage cache TWIG
            $command .= "rm -rf htdocs/tmp/twig;" ;
            // MAJ composer
            $command .= "composer install --no-dev";
            opcache_reset();
    		$output = shell_exec($command);
            opcache_reset();
    		return ['result' => true, 'output' => $output];
    	} else {
    		return ['result' => false, 'output' => null];
    	}
    }
}
