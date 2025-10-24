<?php

declare(strict_types=1);

namespace AppBundle\Audit;

use Doctrine\DBAL\Connection;

final readonly class AuditRepository
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function save(string $message, ?int $userId, ?string $route): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('afup_audit_log')
            ->set('message', ':message')
            ->setParameter('message', $message)
        ;

        if ($userId) {
            $query->set('user_id', ':userId');
            $query->setParameter('userId', $userId);
        }

        if ($route) {
            $query->set('route', ':route');
            $query->setParameter('route', $route);
        }

        $query->executeStatement();
    }
}
