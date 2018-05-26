<?php

require 'vendor/autoload.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds' => __DIR__ . '/db/seeds',
    ],
    'environments' =>
        [
            'default_migration_table' => 'phinxlog',
            'development' => [
                'adapter' => 'mysql',
                'host' => $container->getParameter('database_host'),
                'name' => $container->getParameter('database_name'),
                'user' => $container->getParameter('database_user'),
                'pass' => $container->getParameter('database_password'),
                'port' => $container->getParameter('database_port'),
                'charset' => 'utf8mb4',
            ]
        ]
];
