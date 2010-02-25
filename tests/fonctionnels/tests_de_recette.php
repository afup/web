<?php

define('AFUP_CHEMIN_RACINE', dirname(__FILE__) . '/../site/');
define('CHEMIN_APPLICATION', 'http://localhost/gestafup/trunk/site/');

require_once AFUP_CHEMIN_RACINE . 'include/configuration.inc.php';
require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Base_De_Donnees.php';

require_once 'simpletest/web_tester.php';
require_once 'simpletest/reporter.php';
require_once 'simpletest/browser.php';

class TestAdministration extends WebTestCase {
    
    function setUp() {
    	global $configuration;
		$bdd = new AFUP_Base_De_Donnees($configuration['bdd']['hote'],
	                                $configuration['bdd']['base'],
	                                $configuration['bdd']['utilisateur'],
	                                $configuration['bdd']['mot_de_passe']);
        $bdd->executerFichier('../sql/desinstallation.sql');
        $bdd->executerFichier('../sql/installation.sql');

        $this->get(CHEMIN_APPLICATION . 'administration/index.php');
        $this->setField('utilisateur', 'admin');
        $this->setField('mot_de_passe', 'pass');
        $this->clickSubmit('Se connecter');
        
    }

    function testPasDeConnexionAvecMauvaisMotDePasse() {
        $this->assertTitle('Accueil - Administration AFUP');
        $this->clickLink('Se déconnecter');
        $this->setField('utilisateur', 'bidon');
        $this->setField('mot_de_passe', 'bidon');
        $this->clickSubmit('Se connecter');
        $this->assertTitle('Connexion - Administration AFUP');
        $this->assertWantedPattern('/La connexion a échoué./');
    }

    function testDeconnexion() {
        $this->assertTitle('Accueil - Administration AFUP');
        $this->clickLink('Se déconnecter');
        $this->assertTitle('Connexion - Administration AFUP');
    }

    function testAjouterUnNouveauMembre() {
        $this->clickLink('Personnes physiques');
        $this->clickLink('Ajouter une personne physique');
        $this->assertWantedPattern('/Ajouter une personne physique/');
        $this->setField('nom', 'penet');
        $this->setField('prenom', 'perrick');
        $this->setField('email', 'perrick@noparking.net');
        $this->setField('adresse', '10 rue stappaert');
        $this->setField('code_postal', '59000');
        $this->setField('ville', 'lille');
        $this->setField('login', 'perrick');
        $this->setField('mot_de_passe', 'perrick');
        $this->setField('confirmation_mot_de_passe', 'mauvais-perrick');
        $this->clickSubmit('Ajouter');
        $this->assertWantedPattern('/Le mot de passe et sa confirmation ne concordent pas./');
        $this->setField('mot_de_passe', 'perrick');
        $this->setField('confirmation_mot_de_passe', 'perrick');
        $this->clickSubmit('Ajouter');
        $this->clickLink('Poursuivre');
        $this->clickLink('Personnes physiques');
        $this->assertWantedPattern('/penet/');
    }

    function testAjouterUnMembreEnDoublon() {
        $this->clickLink('Personnes physiques');
        $this->clickLink('Ajouter une personne physique');
        $this->assertWantedPattern('/Ajouter une personne physique/');
        $this->setField('nom', 'penet');
        $this->setField('prenom', 'perrick');
        $this->setField('email', 'perrick@noparking.net');
        $this->setField('adresse', '10 rue stappaert');
        $this->setField('code_postal', '59000');
        $this->setField('ville', 'lille');
        $this->setField('login', 'perrick');
        $this->setField('mot_de_passe', 'perrick');
        $this->setField('confirmation_mot_de_passe', 'perrick');
        $this->clickSubmit('Ajouter');
        $this->clickLink('Poursuivre');
        $this->clickLink('Personnes physiques');
        $this->clickLink('Ajouter une personne physique');
        $this->setField('nom', 'penet2');
        $this->setField('prenom', 'perrick2');
        $this->setField('email', 'perrick2@noparking.net');
        $this->setField('adresse', '10 rue stappaert');
        $this->setField('code_postal', '59000');
        $this->setField('ville', 'lille');
        $this->setField('login', 'perrick');
        $this->setField('mot_de_passe', 'perrick');
        $this->setField('confirmation_mot_de_passe', 'perrick');
        $this->clickSubmit('Ajouter');
        $this->clickLink('Poursuivre');
        $this->clickLink('Personnes physiques');
        $this->assertNoUnwantedPattern('/penet2/');
    }

	function testCotisationPersonnePhysiqueSansPersonneMorale() {
        $this->clickLink('Personnes physiques');
        $this->assertLinkById('cotisations_1');
	}

	function testAjouterUneCotisation() {
        $this->clickLink('Personnes physiques');
        $this->clickLinkById('cotisations_1');
        $this->clickLink('Ajouter une cotisation');
        $this->setField('montant', 10);
        $this->setField('type_reglement', 1);
        $this->setField('date_fin[d]', 1);
        $this->setField('date_fin[F]', 1);
        $this->setField('date_fin[Y]', 2005);
        $this->clickSubmit('Ajouter');
        $this->clickLink('Poursuivre');
        $this->assertWantedPattern('/10.00 &euro;/');
        $this->assertWantedPattern('/par chèque/');
        $this->clickLink('Ajouter une cotisation');
        $this->setField('montant', 100);
        $this->setField('date_fin[d]', 1);
        $this->setField('date_fin[F]', 1);
        $this->setField('date_fin[Y]', 2005);
        $this->clickSubmit('Ajouter');
        $this->clickLink('Poursuivre');
        $this->showSource();
        $this->assertWantedPattern('/100.00 &euro;/');
        $this->assertWantedPattern('/en espèces/');
	}
}
	
$test = &new TestAdministration();
$test->run(new HtmlReporter());

?>