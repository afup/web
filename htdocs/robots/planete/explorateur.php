<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use PlanetePHP\FeedCrawler;
use Symfony\Component\Debug\Debug;

set_time_limit(0);

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../../../vendor/autoload.php';

$env = getenv('SYMFONY_ENV') ?: 'dev';
$debug = getenv('SYMFONY_DEBUG') !== '0' && $env !== 'prod';

if ($debug) {
    Debug::enable();
}

$kernel = new AppKernel($env, $debug);
$kernel->boot();
$container = $kernel->getContainer();
/** @var FeedCrawler $crawler */
$crawler = $container->get(FeedCrawler::class);
$crawler->crawl();
