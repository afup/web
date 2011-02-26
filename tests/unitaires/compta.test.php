<?php

require_once dirname(__FILE__) . '/../../sources/Afup/Bootstrap/Simpletest/Unit.php';

require_once 'Afup/AFUP_Base_De_Donnees.php';

class tests_Compta extends UnitTestCase {
    public $bdd;
    
    function __construct() {
        $this->bdd = new AFUP_Base_De_Donnees('localhost', 'afup_test', 'root', '');
        
        $this->bdd->executer("DROP TABLE IF EXISTS `compta`");
        $this->bdd->executer("CREATE TABLE `compta` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `idclef` varchar(20) NOT NULL,
		  `idoperation` tinyint(5) NOT NULL,
		  `idcategorie` int(11) NOT NULL,
		  `date_ecriture` date NOT NULL,
		  `nom_frs` varchar(50) NOT NULL,
		  `montant` double(11,2) NOT NULL,
		  `description` varchar(255) NOT NULL,
		  `numero` varchar(50) NOT NULL,
		  `idmode_regl` tinyint(5) NOT NULL,
		  `date_regl` date NOT NULL,
		  `obs_regl` varchar(255) NOT NULL,
		  `idevenement` tinyint(5) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM;");
    }
    
    function test_importerFichierBanque() {
   }
}
