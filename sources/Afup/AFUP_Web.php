<?php
require_once dirname(__FILE__).'/AFUP_Configuration.php';
require_once dirname(__FILE__).'/AFUP_Base_De_Donnees.php';

class AFUP_Web {
    public $route = "";
    public $content;
    public $title;
    
    function mettreAJour($update = false) {
    	if ($update === true) {
    		$command = "svn export --force --username ".$GLOBALS['conf']->obtenir('svn|username')." --password ".$GLOBALS['conf']->obtenir('svn|password');
    		$command .= " ".$GLOBALS['conf']->obtenir('svn|url').$GLOBALS['conf']->obtenir('svn|web')." ".dirname(__FILE__)."/../../";
    		$output = shell_exec($command);
    		return true;
    	} else {
    		return false;
    	}
    }
}
