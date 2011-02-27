<?php
/* 
 * Définition des paramètres pour l'exécution des tests
 */
if (file_exists(dirname(__FILE__) . '/config.php')) {
    require_once dirname(__FILE__) . '/config.php';
} else {
    define('TEST_HOST', 'localhost');
    define('TEST_DB', 'afup_test');
    define('TEST_USER', 'root');
    define('TEST_PWD', '');
}
