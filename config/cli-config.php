<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Input\ArgvInput;

require __DIR__.'/../vendor/autoload.php';

$input = new ArgvInput();
$env = $input->getParameterOption(['--env', '-e'], getenv('SYMFONY_ENV') ?: 'dev');
$debug = getenv('SYMFONY_DEBUG') !== '0' && !$input->hasParameterOption(['--no-debug', '']) && $env !== 'prod';
$kernel = new AppKernel($env, $debug);
$kernel->boot();
$connection = $kernel->getContainer()->get(Connection::class);
return ConsoleRunner::createHelperSet($connection);
