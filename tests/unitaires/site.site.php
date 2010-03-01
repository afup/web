<?php

require_once dirname(__FILE__) . '/../../sources/Afup/Bootstrap/Simpletest/Unit.php';

require_once 'afup/AFUP_Base_De_Donnees.php';
require_once 'afup/AFUP_Site.php';

class tests_Site_Article extends UnitTestCase {
    public $article = null;
    public $bdd;
    
    function __construct() {
        $this->bdd = new AFUP_Base_De_Donnees('localhost', 'afup_test', 'root', '');
        $this->bdd->executer('DROP TABLE IF EXISTS `afup_site_article`');
        $this->bdd->executer('CREATE TABLE `afup_site_article` (
		  `id` int(11) NOT NULL auto_increment,
		  `id_site_rubrique` int(11) default NULL,
		  `surtitre` tinytext,
		  `titre` tinytext,
		  `raccourci` varchar(255) default NULL,
		  `descriptif` mediumtext,
		  `chapeau` mediumtext,
		  `contenu` mediumtext,
		  `position` mediumint(9) default NULL,
		  `date` int(11) default NULL,
		  `etat` tinyint(4) default NULL,
		  `id_personne_physique` smallint(5) unsigned default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
        $this->bdd->executer('DROP TABLE IF EXISTS `afup_site_rubrique`');
        $this->bdd->executer('CREATE TABLE `afup_site_rubrique` (
		  `id` int(11) NOT NULL auto_increment,
		  `id_parent` int(11) default NULL,
		  `nom` tinytext,
		  `raccourci` varchar(255) default NULL,
		  `contenu` mediumtext,
		  `descriptif` tinytext,
		  `position` mediumint(9) default NULL,
		  `date` int(11) default NULL,
		  `etat` tinyint(4) default NULL,
		  `id_personne_physique` smallint(5) unsigned default NULL,
		  `icone` varchar(255) default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }
    
    function setUp() {
        $this->article = new AFUP_Site_Article(0, $this->bdd);
        $this->article->id = 0;
        $this->article->surtitre = "En début de page...";
        $this->article->titre = "Le titre complet";
        $this->article->raccourci = "la-route-url";
        $this->article->descriptif = "Dans la zone annexe";
        $this->article->chapeau = "En dessous du titre";
        $this->article->contenu = "Le corps complet";
        $this->article->date = mktime(0, 0, 0, 10, 3, 2008);
        $this->article->etat = 0;
    }
    
    function testConversionDesTiretsEnListeHTML() {
        $texte = "Le début
- element 1
- element 2";
        
        $rendu = "Le début
<ul>
<li>element 1</li>
<li>element 2</li>
</ul>";
        
        $this->assertEqual(AFUP_Site::transformer_liste_spip($texte), $rendu);
    }

    function testConversionDesTiretsEnListeHTMLTroisElements() {
        $texte = "Le début
- element 1
- element 2
- element 3";

        $rendu = "Le début
<ul>
<li>element 1</li>
<li>element 2</li>
<li>element 3</li>
</ul>";

        $this->assertEqual(AFUP_Site::transformer_liste_spip($texte), $rendu);
    }

    function testConversionDesTiretsEnListeHTMLDeuxListes() {
        $texte = "Le début
- element 1
- element 2

Entre deux

- element 3
- element 4

La fin";

        $rendu = "Le début
<ul>
<li>element 1</li>
<li>element 2</li>
</ul>

Entre deux

<ul>
<li>element 3</li>
<li>element 4</li>
</ul>

La fin";

        $this->assertEqual(AFUP_Site::transformer_liste_spip($texte), $rendu);
    }

    function testConversionDesTiretsPasDansUneLigne() {
	        $texte = "Titre - Article";

	        $rendu = "Titre - Article";

	        $this->assertEqual(AFUP_Site::transformer_liste_spip($texte), $rendu);
	    }


    function testUnArticleAvecEtatAZeroNePeutSeCharger() {
        $this->article->etat = 0;
        
        $this->assertFalse($this->article->corps());
        $this->assertFalse($this->article->annexe());
    }

    function testUnArticleAvecEtatAUnSeChargeDansUnContenuSurDeuxZones() {
        $this->article->etat = 1;
        
        $this->assertEqual($this->article->route(), "index.php/rubrique/0/la-route-url");
        
        $this->assertPattern("/<div class=\"surtitre\">En début de page...<\/div>/", $this->article->corps());
        $this->assertPattern("/Le titre complet/", $this->article->titre());
        $this->assertPattern("/<div class=\"chapeau\">En dessous du titre<\/div>/", $this->article->corps());
        $this->assertPattern("/<div class=\"contenu\">Le corps complet<\/div>/", $this->article->corps());

        $this->assertPattern("/03\/10\/2008/", $this->article->date());
        $this->assertNoPattern("/<ul class=\"articles\">/", $this->article->annexe());
    }

    function testEnregistrerUnArticleLuiDonneUnId() {
        $this->assertTrue($this->article->inserer());
        $this->assertEqual($this->article->id, 1);
    }
    
    function testLeFilDArianeSeComposeAvecLeTitreEtLesRubriques() {
        $rubrique = new AFUP_Site_Rubrique(0, $this->bdd);
        $rubrique->id_parent = 0;
        $rubrique->raccourci = "rubrique-conteneur";
        $rubrique->nom = "Rubrique Conteneur";
        $rubrique->inserer();
        
        $this->article->id_site_rubrique = $rubrique->id;
        $this->article->inserer();

        $this->assertPattern("/href/", $this->article->fil_d_ariane());
        $this->assertPattern("/<a href=\".*\">.*<\/a>/", $this->article->fil_d_ariane());
        $this->assertPattern("/".$this->article->titre."/", $this->article->fil_d_ariane());
    }
}

class tests_Site_Rubrique extends UnitTestCase {
    public $article = null;
    public $bdd;
    
    function __construct() {
        $this->bdd = new AFUP_Base_De_Donnees('localhost', 'afup_test', 'root', '');
        $this->bdd->executer('DROP TABLE IF EXISTS `afup_site_rubrique`');
        $this->bdd->executer('CREATE TABLE `afup_site_rubrique` (
		  `id` int(11) NOT NULL auto_increment,
		  `id_parent` int(11) default NULL,
		  `nom` tinytext,
		  `raccourci` varchar(255) default NULL,
		  `contenu` mediumtext,
		  `descriptif` tinytext,
		  `position` mediumint(9) default NULL,
		  `date` int(11) default NULL,
		  `etat` tinyint(4) default NULL,
		  `id_personne_physique` smallint(5) unsigned default NULL,
		  `icone` varchar(255) default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }
    
    function setUp() {
        $this->rubrique = new AFUP_Site_Rubrique(0, $this->bdd);
        $this->rubrique->id = 0;
        $this->rubrique->id_parent = 0;
        $this->rubrique->nom = "Le titre complet";
        $this->rubrique->raccourci = "la-route-url";
        $this->rubrique->descriptif = "Dans la zone annexe";
        $this->rubrique->contenu = "Le corps complet";
        $this->rubrique->date = mktime(0, 0, 0, 10, 3, 2008);
        $this->rubrique->etat = 0;
    }
    
    function testUneRubriqueAvecEtatAZeroNePeutSeCharger() {
        $this->rubrique->etat = 0;
        
        $this->assertFalse($this->rubrique->corps());
        $this->assertFalse($this->rubrique->annexe());
    }

    function testUnArticleAvecEtatAUnSeChargeDansUnContenuSurDeuxZones() {
        $this->rubrique->etat = 1;
        
        $this->assertEqual($this->rubrique->route(), "index.php/la-route-url/0");
        
        $this->assertPattern("/Le titre complet/", $this->rubrique->nom);
        $this->assertPattern("/<div class=\"contenu\">Le corps complet<\/div>/", $this->rubrique->corps());

        $this->assertPattern("/03\/10\/2008/", $this->rubrique->date());
        $this->assertNoPattern("/<ul class=\"articles\">/", $this->rubrique->annexe());
    }
    
    function testEnregistrerUneRubriqueLuiDonneUnId() {
        $this->assertTrue($this->rubrique->inserer());
        $this->assertEqual($this->rubrique->id, 1);
    }
    
    function testLeFilDArianeSeComposeAvecLesSousRubriques() {
        $this->rubrique->inserer();

        $this->assertPattern("/href/", $this->rubrique->fil_d_ariane());
        $this->assertPattern("/<a href=\".*\">.*<\/a>/", $this->rubrique->fil_d_ariane());
        
        $rubrique = new AFUP_Site_Rubrique(0, $this->bdd);
        $rubrique->id_parent = $this->rubrique->id;
        $rubrique->raccourci = "rubrique-fille";
        $rubrique->nom = "Rubrique Fille";
        $rubrique->inserer();
        
        $this->assertPattern("/".$rubrique->nom."/", $rubrique->fil_d_ariane());
    }
}