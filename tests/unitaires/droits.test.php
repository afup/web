<?php

require_once dirname(__FILE__) . '/config.dist.php';

require_once dirname(__FILE__) . '/../../sources/Afup/Bootstrap/Simpletest/Unit.php';

require_once 'Afup/AFUP_Base_De_Donnees.php';
require_once 'Afup/AFUP_Droits.php';

class tests_Droits extends UnitTestCase {
    public $bdd;
    public $pages = array();
    
    function __construct() {
        $_SESSION['afup_login'] = true;
        $_SESSION['afup_mot_de_passe'] = true;
        $this->bdd = new AFUP_Base_De_Donnees(TEST_HOST, TEST_DB, TEST_USER, TEST_PWD);
        $this->pages = array(
			'accueil' => array(
				'nom' => 'Accueil',
		    	'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		    ),
			'membre' => array(
				'nom' => 'Mon compte',
		        'elements' => array(
				    'cotisation' => array(
				        'nom' => 'Cotisations',
				        'niveau' => AFUP_DROITS_NIVEAU_MEMBRE,
		                'module' => 0,
				    ),
				),
		    ),
			'membres' => array(
				'nom' => 'Membres',
		        'elements' => array(
				    'membres_cotisations' => array(
				        'nom' => 'Cotisations',
				        'niveau' => AFUP_DROITS_NIVEAU_ADMINISTRATEUR,
		                'module' => 1,
		            ),
				),
		    ),
		);
    }
    
    function test_accepterAffichagePageAccueilEtRefuserAutre() {
        $_SESSION['afup_niveau'] = AFUP_DROITS_NIVEAU_MEMBRE;
        $droits = new AFUP_Droits($this->bdd);
        $this->assertTrue($droits->chargerToutesLesPages($this->pages));
        $this->assertTrue($droits->verifierDroitSurLaPage('accueil'));
        $this->assertFalse($droits->verifierDroitSurLaPage('autre'));
    }

    function test_accepterAffichagePageCotisationDansPagesA2Niveaux() {
        $_SESSION['afup_niveau'] = AFUP_DROITS_NIVEAU_MEMBRE;
        $droits = new AFUP_Droits($this->bdd);
        $this->assertTrue($droits->chargerToutesLesPages($this->pages));
        $this->assertTrue($droits->verifierDroitSurLaPage('cotisation'));
        $this->assertTrue($droits->verifierDroitSurLaPage('accueil'));
        $this->assertFalse($droits->verifierDroitSurLaPage('membres_cotisations'));
        $this->assertFalse($droits->verifierDroitSurLaPage('autre'));
    }
    
    function test_autoriserUnePageAvecDroitsComplementaires() {
        $_SESSION['afup_niveau'] = AFUP_DROITS_NIVEAU_MEMBRE;
        $_SESSION['afup_niveau_modules'] = AFUP_DROITS_NIVEAU_MEMBRE.AFUP_DROITS_NIVEAU_ADMINISTRATEUR;
        $droits = new AFUP_Droits($this->bdd);
        $this->assertTrue($droits->chargerToutesLesPages($this->pages));
        $this->assertTrue($droits->verifierDroitSurLaPage('cotisation'));
        $this->assertTrue($droits->verifierDroitSurLaPage('accueil'));
        $this->assertTrue($droits->verifierDroitSurLaPage('membres_cotisations'));
        $this->assertFalse($droits->verifierDroitSurLaPage('autre'));
    }
    
    function test_accepterAffichageToutesLesPagesPourUnAdminstrateur() {
        $_SESSION['afup_niveau'] = AFUP_DROITS_NIVEAU_ADMINISTRATEUR;
        $_SESSION['afup_niveau_modules'] = "";
        $droits = new AFUP_Droits($this->bdd);
        $this->assertTrue($droits->chargerToutesLesPages($this->pages));
        $this->assertTrue($droits->verifierDroitSurLaPage('cotisation'));
        $this->assertTrue($droits->verifierDroitSurLaPage('accueil'));
        $this->assertTrue($droits->verifierDroitSurLaPage('autre'));
    }
    
    function test_dechargerToutesLesPagesPourUnAdministrateur() {
        $_SESSION['afup_niveau'] = AFUP_DROITS_NIVEAU_ADMINISTRATEUR;
        $droits = new AFUP_Droits($this->bdd);
        $droits->chargerToutesLesPages($this->pages);
		$this->assertEqual($droits->dechargerToutesLesPages(), $this->pages);
    }
    
    function test_lesPagesDechargesDUnMembreNeContiennentPasTout() {
        $_SESSION['afup_niveau'] = AFUP_DROITS_NIVEAU_MEMBRE;
        $droits = new AFUP_Droits($this->bdd);
        $droits->chargerToutesLesPages($this->pages);
        $pages = $droits->dechargerToutesLesPages();
        $this->assertEqual($pages['accueil'], $this->pages['accueil']);
        $this->assertEqual($pages['membre'], $this->pages['membre']);
        $this->assertFalse(isset($pages['membres']));
    }
}