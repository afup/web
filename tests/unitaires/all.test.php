<?php

require_once dirname(__FILE__)."/../simpletest/autorun.php";

class AllTests extends TestSuite {
    function __construct() {
		$nodes = new RecursiveDirectoryIterator(dirname(__FILE__));
		foreach(new RecursiveIteratorIterator($nodes) as $node) {
			if (preg_match('/test\.php$/', $node->getFilename()) and $node->getFilename() != "all.test.php") {
				$this->addFile($node->getPathname());
			}        	
		}
	}
}
