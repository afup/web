<?php
use Afup\Site\Comptabilite\Comptabilite;
use Afup\Site\Utils\Base_De_Donnees;

require_once dirname(__FILE__) . '/config.dist.php';

require_once dirname(__FILE__) . '/../../sources/Afup/Bootstrap/Simpletest/Unit.php';

class tests_Compta extends UnitTestCase {
    public $bdd;
    
    function __construct() {
        $this->bdd = new Base_De_Donnees(TEST_HOST, TEST_DB, TEST_USER, TEST_PWD);
        
        $this->bdd->executer("DROP TABLE IF EXISTS `compta`");
        $this->bdd->executer("CREATE TABLE `compta` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `idclef` varchar(20) NOT NULL,
		  `idoperation` tinyint(5) NOT NULL,
		  `idcategorie` int(11) NOT NULL,
		  `date_ecriture` date NOT NULL,
          `numero_operation` varchar(100) DEFAULT NULL,
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
        $compta = new Comptabilite($this->bdd);
        $this->assertFalse($compta->extraireComptaDepuisCSVBanque(null));
        $this->assertFalse($compta->extraireComptaDepuisCSVBanque(array()));
        $fichierBanque = file(dirname(__FILE__)."/data/banque.csv");
        $this->assertTrue($compta->extraireComptaDepuisCSVBanque($fichierBanque));
        $toutCompta = $compta->obtenirTous();
        $this->assertEqual(4, count($toutCompta));
        $this->assertEqual('2011-11-09', $toutCompta[0]['date_ecriture']);
        $this->assertEqual(3, $toutCompta[0]['idmode_regl']);
        $this->assertEqual(2, $toutCompta[1]['idmode_regl']);
        $this->assertEqual(4, $toutCompta[2]['idmode_regl']);
        $this->assertEqual(4, $toutCompta[3]['idmode_regl']);
        $this->assertEqual(180, $toutCompta[0]['montant']);
        $this->assertEqual(0.65, $toutCompta[1]['montant']);
        $this->assertEqual(0.65, $toutCompta[2]['montant']);
        $this->assertEqual(0.65, $toutCompta[3]['montant']);
   }
}
