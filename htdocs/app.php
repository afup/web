<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

if ($_SERVER['HTTP_HOST'] === 'afup.dev' || isset($_ENV['SF_DEV_ENV'])) {
    if (isset($_SERVER['HTTP_CLIENT_IP'])
        || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', '192.168.42.1']) || php_sapi_name() === 'cli-server')
    ) {
        //header('HTTP/1.0 403 Forbidden');
        //exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
    }

    session_start();

    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require __DIR__.'/../app/autoload.php';
    Debug::enable();

    $kernel = new AppKernel('dev', true);
} else {
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require __DIR__.'/../app/autoload.php';
    include_once __DIR__.'/../var/bootstrap.php.cache';

    session_start();

    $kernel = new AppKernel('prod', false);
}

$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
