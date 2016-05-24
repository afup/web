<?php

use Afup\Site\Tags;

require_once dirname(__FILE__) . '/config.dist.php';

require_once dirname(__FILE__) . '/../../sources/Afup/Bootstrap/Simpletest/Unit.php';

class tests_Tags extends UnitTestCase {
    function __construct() {
        $this->elements = array();
        $this->tags = new Tags($bdd);
    }
    
    function test_extraireTousLesTagsDUneChaine() {
        $this->assertEqual($this->tags->extraireTags("tous les tags"), array("tous", "les", "tags"));   
        $this->assertEqual($this->tags->extraireTags("'tous les' tags"), array("tous les", "tags"));   
        $this->assertEqual($this->tags->extraireTags("'tous les tags"), array("'tous", "les", "tags"));   
        $this->assertEqual($this->tags->extraireTags("afup:tous les tags"), array("afup:tous", "les", "tags"));   
        $this->assertEqual($this->tags->extraireTags("1@tous les tags"), array("1@tous", "les", "tags"));   
        $this->assertEqual($this->tags->extraireTags("aaa test 'test te'"), array("aaa", "test", "test te"));   
    }
    
    function test_preparerFichierDotVierge() {
        $this->assertEqual($this->tags->preparerFichierDot(), "graph G {\n}\n");
        
        $this->elements[] = array (
		  'id_source' => '2',
		  'tag' => 'rien',
		  'id_personne_physique' => '1',
		);
        $this->assertEqual($this->tags->preparerFichierDot($this->elements), "graph G {\n}\n");
    }
    
    function test_fichierDotAvecUnLien() {
        $this->elements[] = array (
		  'id_source' => '2',
		  'tag' => 'ici',
		  'id_personne_physique' => '1',
		);
        $this->assertEqual($this->tags->preparerFichierDot($this->elements), "graph G {\n  rien -- ici;\n}\n");
    }
    
    function test_fichierDotAvecPlusieursLiens() {
         $this->elements[] = array (
		  'id_source' => '2',
		  'tag' => 'l�',
		  'id_personne_physique' => '1',
		);
        $this->assertPattern("/rien -- ici/", $this->tags->preparerFichierDot($this->elements));
        $this->assertPattern("/rien -- l�/", $this->tags->preparerFichierDot($this->elements));
        $this->assertPattern("/ici -- l�/", $this->tags->preparerFichierDot($this->elements));
    }
    
    function test_fichierDotSansDoublons() {
        $this->elements[] = array (
		  'id_source' => '3',
		  'tag' => 'ici',
		  'id_personne_physique' => '2',
		);
        $this->elements[] = array (
		  'id_source' => '3',
		  'tag' => 'l�',
		  'id_personne_physique' => '2',
		);
		$this->assertEqual(substr_count($this->tags->preparerFichierDot($this->elements), "ici -- l�;"), 1);
    }
    
    function test_fichierDotSansDoublonsMajuscules() {
        $this->elements[] = array (
		  'id_source' => '3',
		  'tag' => 'ICI',
		  'id_personne_physique' => '2',
		);
        $this->elements[] = array (
		  'id_source' => '3',
		  'tag' => 'L�',
		  'id_personne_physique' => '2',
		);
		$this->assertEqual(substr_count($this->tags->preparerFichierDot($this->elements), "ici -- l�;"), 1);
    }
    
    function test_fichierDotSansDoublonInverses() {
		$this->elements[] = array (
		  'id_source' => '3',
		  'tag' => 'l�',
		  'id_personne_physique' => '3',
		);
        $this->elements[] = array (
		  'id_source' => '3',
		  'tag' => 'ici',
		  'id_personne_physique' => '3',
		);
        $this->assertNoPattern("/l� -- ici/", $this->tags->preparerFichierDot($this->elements));
    }
    
    function test_fichierDotSansElementVide() {
        $this->elements[] = array (
		  'id_source' => '3',
		  'tag' => '',
		  'id_personne_physique' => '3',
		);
        $this->assertNoPattern("/l� -- /", $this->tags->preparerFichierDot($this->elements));
    }
    
    function test_fichierDotSansTagsQuiCommencentAvecChiffre() {
		$this->elements[] = array (
		  'id_source' => '3',
		  'tag' => '12l�',
		  'id_personne_physique' => '3',
		);
        $this->assertNoPattern("/12l�/", $this->tags->preparerFichierDot($this->elements));
    }
    
    function test_fichierDotSansTagsPonctuesNiEspaces() {
		$this->elements[] = array (
		  'id_source' => '3',
		  'tag' => 'un.l�',
		  'id_personne_physique' => '3',
		);
        $this->assertNoPattern("/un.l�/", $this->tags->preparerFichierDot($this->elements));
        $this->assertPattern("/unl�/", $this->tags->preparerFichierDot($this->elements));

        $this->elements[] = array (
		  'id_source' => '3',
		  'tag' => 'deux l�',
		  'id_personne_physique' => '3',
		);
        $this->assertNoPattern("/deux l�/", $this->tags->preparerFichierDot($this->elements));
        $this->assertPattern("/deuxl�/", $this->tags->preparerFichierDot($this->elements));
    }
}