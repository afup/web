<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Symfony\Component\HttpFoundation\Request;

$isDevEnv = isset($_ENV['SYMFONY_ENV']) && $_ENV['SYMFONY_ENV'] == 'dev';
$isTestEnv = isset($_ENV['SYMFONY_ENV']) && $_ENV['SYMFONY_ENV'] == 'test';

if ($_SERVER['HTTP_HOST'] === 'afup.dev' || $isDevEnv || $isTestEnv) {
    if (!$isDevEnv && !$isTestEnv
        &&
        (
            isset($_SERVER['HTTP_CLIENT_IP'])
            || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            || !in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', '192.168.42.1']) && php_sapi_name() !== 'cli-server'
        )
    ) {
        header('HTTP/1.0 403 Forbidden');
        exit('You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.');
    }

    /** @var ClassLoader $loader */
    $loader = require __DIR__ . '/../vendor/autoload.php';

    $kernel = $isDevEnv ? new AppKernel('dev', true) : new AppKernel('test', true);
} else {
    /** @var ClassLoader $loader */
    $loader = require __DIR__ . '/../vendor/autoload.php';

    $kernel = new AppKernel('prod', false);
}

$request = Request::createFromGlobals();

$proxies = [
    '127.0.0.1',
];
$ccReverseProxyIps = getenv('CC_REVERSE_PROXY_IPS');
if (false !== $ccReverseProxyIps) {
    $proxies = array_merge($proxies, explode(',', $ccReverseProxyIps));
}

Request::setTrustedProxies(
    $proxies,
    Request::HEADER_X_FORWARDED_FOR
    | Request::HEADER_X_FORWARDED_HOST
    | Request::HEADER_X_FORWARDED_PORT
    | Request::HEADER_X_FORWARDED_PROTO
);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
