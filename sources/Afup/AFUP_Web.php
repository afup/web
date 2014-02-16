<?php
require_once dirname(__FILE__).'/AFUP_Configuration.php';
require_once dirname(__FILE__).'/AFUP_Base_De_Donnees.php';

class AFUP_Web {
    function mettreAJour($update = false) {
    	if ($update === true) {
            $command = "cd ".$GLOBALS['conf']->obtenir('git|local_repo')."; git stash; git pull origin master; git stash pop;";
            $command .= "git checkout-index -f -a --prefix=/".$GLOBALS['conf']->obtenir('git|local_export');
            apc_clear_cache();
    		$output = shell_exec($command);
            apc_clear_cache();
    		return true;
    	} else {
    		return false;
    	}
    }
}
