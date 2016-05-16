<?php
require_once dirname(__FILE__).'/AFUP_Configuration.php';
require_once dirname(__FILE__).'/AFUP_Base_De_Donnees.php';

class AFUP_Web {
    function mettreAJour($update = false) {
    	if ($update === true) {
            $command = "cd ".$GLOBALS['conf']->obtenir('git|local_repo')."; git stash;";
            $command .= "git checkout master; git pull origin;";
            $command .= "git checkout ".$GLOBALS['conf']->obtenir('git|deployed_branch')."; git stash pop;";
            $command .= "git checkout-index -f -a --prefix=/".$GLOBALS['conf']->obtenir('git|local_export') . " ; ";
            $command .= "cd /".$GLOBALS['conf']->obtenir('git|local_export')."; composer install --no-dev";
            opcache_reset();
    		$output = shell_exec($command);
            opcache_reset();
    		return true;
    	} else {
    		return false;
    	}
    }
}
