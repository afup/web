<?php

use Composer\Autoload\ClassLoader;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

// Petit hack pour pouvoir faire un require 'app.php' au lieu d'une redirection Apache
$_SERVER['SCRIPT_NAME'] = '';
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR;

if (!defined('AFUP_GLOBAL_MENU_PREFIX')) {
    define('AFUP_GLOBAL_MENU_PREFIX', 'https://afup.org');
}

$isDevEnv = isset($_ENV['SYMFONY_ENV']) && $_ENV['SYMFONY_ENV'] == 'dev';
$isTestEnv = isset($_ENV['SYMFONY_ENV']) && $_ENV['SYMFONY_ENV'] == 'test';

if ($_SERVER['HTTP_HOST'] === 'afup.dev' || $isDevEnv || $isTestEnv) {
    if (!($isDevEnv || $isTestEnv)
        &&
        (
            isset($_SERVER['HTTP_CLIENT_IP'])
            || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', '192.168.42.1']) || php_sapi_name() === 'cli-server')
        )
    ) {
        header('HTTP/1.0 403 Forbidden');
        exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
    }

    session_start();

    /** @var ClassLoader $loader */
    $loader = require __DIR__.'/../../../vendor/autoload.php';
    Debug::enable();

    if ($isDevEnv) {
        $kernel = new PlaneteAppKernel('dev', true);
    } else {
        $kernel = new PlaneteAppKernel('test', true);
    }
} else {
    /** @var ClassLoader $loader */
    $loader = require __DIR__.'/../../../vendor/autoload.php';

    session_start();

    $kernel = new PlaneteAppKernel('prod', false);
}

$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
