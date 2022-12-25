<?php

require 'vendor/autoload.php';

$environments = [
    'default_migration_table' => 'phinxlog',
];

if (is_file(__DIR__ . '/vendor/behat/behat/composer.json')) {
    $kernelDev = new AppKernel('dev', true);
    $kernelDev->boot();
    $containerDev = $kernelDev->getContainer();
    $environments['development'] = [
        'adapter' => 'mysql',
        'host' => $containerDev->getParameter('database_host'),
        'name' => $containerDev->getParameter('database_name'),
        'user' => $containerDev->getParameter('database_user'),
        'pass' => $containerDev->getParameter('database_password'),
        'port' => $containerDev->getParameter('database_port'),
        'charset' => 'utf8mb4',
    ];

    $kernelDev = new AppKernel('test', true);
    $kernelDev->boot();
    $containerDev = $kernelDev->getContainer();
    $environments['test'] = [
        'adapter' => 'mysql',
        'host' => $containerDev->getParameter('database_host'),
        'name' => $containerDev->getParameter('database_name'),
        'user' => $containerDev->getParameter('database_user'),
        'pass' => $containerDev->getParameter('database_password'),
        'port' => $containerDev->getParameter('database_port'),
        'charset' => 'utf8mb4',
    ];
}

$kernelProd = new AppKernel('prod', true);
$kernelProd->boot();
$containerProd = $kernelProd->getContainer();

$environments['production'] = [
    'adapter' => 'mysql',
    'host' => $containerProd->getParameter('database_host'),
    'name' => $containerProd->getParameter('database_name'),
    'user' => $containerProd->getParameter('database_user'),
    'pass' => $containerProd->getParameter('database_password'),
    'port' => $containerProd->getParameter('database_port'),
    'charset' => 'utf8mb4',
];

return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds' => __DIR__ . '/db/seeds',
    ],
    'environments' => $environments,
];
