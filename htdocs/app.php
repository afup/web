<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$isDevEnv = isset($_ENV['SYMFONY_ENV']) && $_ENV['SYMFONY_ENV'] == 'dev';

if ($_SERVER['HTTP_HOST'] === 'afup.dev' || $isDevEnv) {
    if (!$isDevEnv
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

    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require __DIR__.'/../vendor/autoload.php';
    Debug::enable();

    $kernel = new AppKernel('dev', true);
} else {
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require __DIR__.'/../vendor/autoload.php';

    session_start();

    $kernel = new AppKernel('prod', false);
}

$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
