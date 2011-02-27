<?php

require_once dirname(__FILE__) . '/config.dist.php';

require_once dirname(__FILE__) . '/../../sources/Afup/Bootstrap/Simpletest/Unit.php';

require_once 'Afup/AFUP_Base_De_Donnees.php';
require_once 'Afup/AFUP_Personnes_Physiques.php';
require_once 'Afup/AFUP_Oeuvres.php';
require_once 'Afup/AFUP_Logs.php';
require_once 'Afup/AFUP_Planete_Flux.php';
require_once 'Afup/AFUP_Planete_Billet.php';

class tests_Oeuvres extends UnitTestCase {
    public $bdd;
    
    function __construct() {
        $this->bdd = new AFUP_Base_De_Donnees(TEST_HOST, TEST_DB, TEST_USER, TEST_PWD);
        
        $this->bdd->executer("DROP TABLE IF EXISTS `afup_oeuvres`");
        $this->bdd->executer("CREATE TABLE `afup_oeuvres` (
		  `id` int(11) NOT NULL auto_increment,
		  `id_personne_physique` smallint(5) unsigned default NULL,
		  `categorie` varchar(255) default NULL,
		  `valeur` smallint(5) default NULL,
		  `date` int(11) default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        
        $this->bdd->executer("DROP TABLE IF EXISTS `afup_personnes_physiques`");
        $this->bdd->executer("CREATE TABLE `afup_personnes_physiques` (
		  `id` smallint(5) unsigned NOT NULL auto_increment,
		  `id_personne_morale` smallint(5) unsigned default NULL,
		  `login` varchar(30) NOT NULL default '',
		  `mot_de_passe` varchar(32) NOT NULL default '',
		  `niveau` tinyint(3) unsigned NOT NULL default '0',
		  `niveau_modules` varchar(3) NOT NULL,
		  `civilite` varchar(4) NOT NULL default '',
		  `nom` varchar(40) NOT NULL default '',
		  `prenom` varchar(40) NOT NULL default '',
		  `email` varchar(100) NOT NULL default '',
		  `adresse` text NOT NULL,
		  `code_postal` varchar(10) NOT NULL default '',
		  `ville` varchar(50) NOT NULL default '',
		  `id_pays` char(2) NOT NULL default '',
		  `telephone_fixe` varchar(20) default NULL,
		  `telephone_portable` varchar(20) default NULL,
		  `etat` tinyint(3) unsigned NOT NULL default '0',
		  `date_relance` int(11) unsigned default NULL,
		  `compte_svn` varchar(100) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `pays` (`id_pays`),
		  KEY `personne_morale` (`id_personne_morale`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1");
        
        $personnes_physiques = new AFUP_Personnes_Physiques($this->bdd);
        $personnes_physiques->ajouter(0, "ArnaudLimbourg", uniqid(), AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            "", 0, "Limbourg", "Arnaud", "test@test.test", "adresse", "code_postal", "ville", 0,
            "telephone_fixe", "telephone_portable", 1, "arnaud");
        $personnes_physiques->ajouter(0, "PerrickPenet", uniqid(), AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            "", 0, "Penet", "Perrick", "test@test.test", "adresse", "code_postal", "ville", 0,
            "telephone_fixe", "telephone_portable", 1, "perrick");
        $personnes_physiques->ajouter(0, "SarahHaim", uniqid(), AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
            "", 0, "Haim", "Sarah", "test@test.test", "adresse", "code_postal", "ville", 0,
            "telephone_fixe", "telephone_portable", 1, "sarah");
        
        $this->bdd->executer("DROP TABLE IF EXISTS `afup_logs`");
        $this->bdd->executer("CREATE TABLE `afup_logs` (
		  `id` mediumint(8) unsigned NOT NULL auto_increment,
		  `date` int(11) unsigned NOT NULL default '0',
		  `id_personne_physique` smallint(5) unsigned NOT NULL default '0',
		  `texte` varchar(255) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  KEY `id_personne_physique` (`id_personne_physique`)
		);");
        
        $this->bdd->executer("DROP TABLE IF EXISTS `afup_planete_billet`");
        $this->bdd->executer("CREATE TABLE `afup_planete_billet` (
		  `id` int(11) NOT NULL auto_increment,
		  `afup_planete_flux_id` int(11) default NULL,
		  `clef` varchar(255) default NULL,
		  `titre` mediumtext,
		  `url` varchar(255) default NULL,
		  `maj` int(11) default NULL,
		  `auteur` mediumtext,
		  `resume` mediumtext,
		  `contenu` mediumtext,
		  `etat` tinyint(4) default NULL,
		  PRIMARY KEY  (`id`)
		);");
		
		$this->bdd->executer("DROP TABLE IF EXISTS `afup_planete_flux`");
        $this->bdd->executer("CREATE TABLE `afup_planete_flux` (
		  `id` int(11) NOT NULL auto_increment,
		  `nom` varchar(255) default NULL,
		  `url` varchar(255) default NULL,
		  `feed` varchar(255) default NULL,
		  `etat` tinyint(4) default NULL,
		  `id_personne_physique` smallint(5) unsigned default NULL,
		  PRIMARY KEY  (`id`)
		);");
    }
    
    function test_obtenirIdDepuisCompteSVN() {
        $personnes_physiques = new AFUP_Personnes_Physiques($this->bdd);
        $this->assertEqual($personnes_physiques->obtenirIdDepuisCompteSVN("arnaud"), 1);    
    }
    
    function test_extraireOeuvresDepuisLogSVN() {
        $oeuvres = new AFUP_Oeuvres($this->bdd);
        $logsvn = uniqid();
        $this->assertFalse($oeuvres->extraireOeuvresDepuisLogSVN($logsvn));
        
        $logsvn = dirname(__FILE__)."/data/afup.svn.log";
        $this->assertTrue($oeuvres->extraireOeuvresDepuisLogSVN($logsvn));
        $this->assertTrue(isset($oeuvres->details['svn'][1][strtotime("2007-12-1")]));
        $this->assertTrue($oeuvres->details['svn'][1][strtotime("2007-12-1")], 1);

        $this->assertTrue(isset($oeuvres->details['svn'][3][strtotime("2008-2-1")]));
        $this->assertTrue($oeuvres->details['svn'][3][strtotime("2008-2-1")], 3);
    }
    
    function test_enregistrer() {
        $oeuvres = new AFUP_Oeuvres($this->bdd);
        $oeuvres->details['svn'][3][strtotime("2008-2-1")] = 3;
        $this->assertTrue($oeuvres->inserer());
    }
    
    function test_obtenirOeuvresDes12DerniersMois() {
        $oeuvres = new AFUP_Oeuvres($this->bdd);
        $oeuvres->details = array(
            'svn' => array(
                3 => array(
                    strtotime('-1 month', time()) => 3,
                    strtotime('+1 month', time()) => 1,
                ),
            ),
        );
        $oeuvres->inserer();
        
        $date = mktime(0, 0, 0, date('m') -1, 1, date('Y'));
        $oeuvresDes12DerniersMois = $oeuvres->obtenirOeuvresSur12Mois(3);
        $this->assertTrue(isset($oeuvresDes12DerniersMois[3]));
        $this->assertTrue(isset($oeuvresDes12DerniersMois[3]['svn']));
        $this->assertTrue(isset($oeuvresDes12DerniersMois[3]['svn'][$date]));
        $this->assertEqual($oeuvresDes12DerniersMois[3]['svn'][$date], 3);

        $sparklinesDes12DerniersMois = $oeuvres->obtenirSparklinesSur12Mois(3);
        $this->assertTrue(isset($sparklinesDes12DerniersMois[3]));
        $this->assertTrue(isset($sparklinesDes12DerniersMois[3]['svn']));
        $this->assertEqual($sparklinesDes12DerniersMois[3]['svn']['liste'], '0,0,3,0,0,0,0,0,0,0,3,0');
        $this->assertEqual($sparklinesDes12DerniersMois[3]['svn']['dernier'], '0');
        $this->assertEqual($sparklinesDes12DerniersMois[3]['svn']['maximum'], '3');
        $this->assertEqual($sparklinesDes12DerniersMois[3]['svn']['minimum'], '0');

        $this->assertEqual(
            $sparklinesDes12DerniersMois[3],
            $oeuvres->obtenirSparklinePersonnelleSur12Mois(3)
        );
        
        $sparklinesDes12DerniersMois = $oeuvres->obtenirSparklinesSur12Mois(321);
        $this->assertTrue(isset($sparklinesDes12DerniersMois[321]));
        $this->assertFalse(isset($sparklinesDes12DerniersMois[3]));

        $sparklinesDes12DerniersMois = $oeuvres->obtenirSparklinesSur12Mois(array(3, 321));
        $this->assertTrue(isset($sparklinesDes12DerniersMois[321]));
        $this->assertTrue(isset($sparklinesDes12DerniersMois[3]));
    }
    
    function test_obtenirPersonnesPhysiquesLesPlusActives() {
        $oeuvres = new AFUP_Oeuvres($this->bdd);
        $this->assertEqual(
            $oeuvres->obtenirPersonnesPhysiquesLesPlusActives(),
            array(3)
        );
        
        $oeuvres->details = array();
        for ($i = 1; $i <= 25; $i++) {
	        $oeuvres->details['svn'][$i] = array(
                mktime(0, 0, 0, date('m') - 1, 1, date('Y')) => $i,
	        );
        }
        $oeuvres->inserer();
        $this->assertEqual(
            $oeuvres->obtenirPersonnesPhysiquesLesPlusActives(),
            array(25, 24, 23, 22, 21, 20, 19, 18)
        );

        $sparklinesDes12DerniersMois = $oeuvres->obtenirSparklinesSur12Mois(array(25, 24));
        $sparklinesParCategorieDes12DerniersMois = $oeuvres->obtenirSparklinesParCategorieDes12DerniersMois(array(25, 24));
        $this->assertIdentical(
            $sparklinesDes12DerniersMois[25]['svn'],
            $sparklinesParCategorieDes12DerniersMois['svn'][25]
        );
    }
    
    function test_extraireOeuvresDepuisLogs() {
        $oeuvres = new AFUP_Oeuvres($this->bdd);
        $this->assertTrue($oeuvres->extraireOeuvresDepuisLogs());
        
        $log = new AFUP_Logs($this->bdd);
        $log->initialiser($this->bdd, 1);
        $log->log("Test ".uniqid());
        
        $premier_du_mois = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $this->assertTrue($oeuvres->extraireOeuvresDepuisLogs());
        $this->assertTrue(isset($oeuvres->details['logs'][1][$premier_du_mois]));
        $this->assertTrue($oeuvres->details['logs'][1][$premier_du_mois], 1);

        $log->log("Test ".uniqid());
        $log->log("Test ".uniqid());
        
        $this->assertTrue($oeuvres->extraireOeuvresDepuisLogs());
        $this->assertTrue($oeuvres->details['logs'][1][$premier_du_mois], 3);
    }
    
    function test_extraireOeuvresDepuisPlanete() {
        $oeuvres = new AFUP_Oeuvres($this->bdd);
        $this->assertTrue($oeuvres->extraireOeuvresDepuisPlanete());
        
        $flux = new AFUP_Planete_Flux($this->bdd);
        $flux->ajouter("Nom", "http://example.com", "http://example.com/atom", 1, 1);
        
        $premier_du_mois = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $this->assertTrue($oeuvres->extraireOeuvresDepuisPlanete());
        $this->assertFalse(isset($oeuvres->details['planete'][1][$premier_du_mois]));
        
        $billet = new AFUP_Planete_Billet($this->bdd);
        $billet->ajouter(1, "key", "Titre", "http://example.com/billet", time(), "Auteur", "R�sum�", "Contenu", AFUP_PLANETE_BILLET_CREUX);

        $this->assertTrue($oeuvres->extraireOeuvresDepuisPlanete());
        $this->assertFalse(isset($oeuvres->details['planete'][1][$premier_du_mois]));
        
        $billet = new AFUP_Planete_Billet($this->bdd);
        $billet->ajouter(1, "key", "Titre bis", "http://example.com/billet", time(), "Auteur", "R�sum�", "Contenu", AFUP_PLANETE_BILLET_PERTINENT);

        $this->assertTrue($oeuvres->extraireOeuvresDepuisPlanete());
        $this->assertTrue(isset($oeuvres->details['planete'][1][$premier_du_mois]));
        $this->assertTrue($oeuvres->details['planete'][1][$premier_du_mois], 1);
    }
}