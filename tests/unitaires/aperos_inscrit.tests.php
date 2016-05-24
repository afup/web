<?php

use Afup\Site\Utils\Base_De_Donnees;

require_once dirname(__FILE__) . '/../../sources/Afup/Bootstrap/Simpletest/Unit.php';

class tests_AFUP_Aperos_Inscrit extends UnitTestCase {
    public $bdd;
    
    function __construct() {
        $this->bdd = new Base_De_Donnees('localhost', 'afup_test', 'root', '');
        $this->bdd->executer('DROP TABLE IF EXISTS `afup_aperos_inscrits`');
        $this->bdd->executer('CREATE TABLE `afup_aperos_inscrits` (
		  `id` int(11) NOT NULL auto_increment,
		  `pseudo` varchar(20) NOT NULL,
		  `mot_de_passe` varchar(100) NOT NULL,
		  `nom` varchar(70) NOT NULL,
		  `prenom` varchar(70) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  `site_web` varchar(255) NOT NULL,
		  `id_ville` int(11) NOT NULL,
		  `date_inscription` int(10) NOT NULL,
		  `etat` tinyint(1) NOT NULL default "0",
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `login` (`pseudo`,`email`)
		) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8');
    }

    function test_authentifier() {
		$inscrit = new \Afup\Site\Aperos\Inscrits($this->bdd);
		$inscrit->values = array(
			'pseudo' => "perrick",
			'mot_de_passe' => "mot_de_passe",
			'etat' => 1, 
		);
		$inscrit->inserer();
		$this->assertTrue($inscrit->authentifier("perrick", "mot_de_passe"));
	}
}
