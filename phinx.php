<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require 'vendor/autoload.php';

$environments = [
    'default_migration_table' => 'phinxlog',
];

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$environments[getenv('APP_ENV') ?: 'dev'] = [
    'adapter' => 'mysql',
    'host' => $_ENV['DATABASE_HOST'],
    'name' => $_ENV['DATABASE_NAME'],
    'user' => $_ENV['DATABASE_USER'],
    'pass' => $_ENV['DATABASE_PASSWORD'],
    'port' => $_ENV['DATABASE_PORT'],
    'charset' => 'utf8mb4',
];

return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds' => __DIR__ . '/db/seeds',
    ],
    'environments' => $environments,
];
