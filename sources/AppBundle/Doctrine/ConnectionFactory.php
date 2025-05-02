<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;

final readonly class ConnectionFactory
{
    public function __invoke(string $url): Connection
    {
        $dsnParser = new DsnParser(['mysql' => 'pdo_mysql']);

        return DriverManager::getConnection($dsnParser->parse($url));
    }
}
