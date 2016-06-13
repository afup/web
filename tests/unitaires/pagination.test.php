<?php

use Afup\Site\Pagination;

require_once dirname(__FILE__) . '/config.dist.php';
require_once dirname(__FILE__) . '/../../sources/Afup/Bootstrap/Simpletest/Unit.php';
require_once 'smarty/Smarty.class.php';

$smarty = new Smarty();
$smarty->template_dir = (dirname(__FILE__).'/../../htdocs/templates/site/');
$smarty->compile_dir = (dirname(__FILE__).'/../../htdocs/cache/tests/');

class tests_Pagination extends UnitTestCase {
    function testPagination69par10() {
        $this->doTest(69, 10, 7);
    }

    function testPagination70par10() {
        $this->doTest(70, 10, 7);
    }

    function testPagination71par10() {
        $this->doTest(71, 10, 8);
    }

    function testPagination71par25() {
        $this->doTest(71, 25, 3);
    }

    function genere_route() {
        return '';
    }

    function doTest($nombre_elements, $elements_par_page, $nombre_pages) {
        $pagination = new Pagination(1, $elements_par_page, $nombre_elements, array($this, 'genere_route'));
        $html = $pagination->__toString();

        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $lis = $doc->getElementsByTagName('li');

        $this->assertEqual($nombre_pages+4, $lis->length);
    }
}
