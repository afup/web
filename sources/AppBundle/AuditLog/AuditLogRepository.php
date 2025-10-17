<?php

declare(strict_types=1);

namespace AppBundle\AuditLog;

use Doctrine\DBAL\Connection;

final readonly class AuditLogRepository
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function save(string $message, ?int $userId, ?string $route): void
    {
        $query = ($qb = $this->connection->createQueryBuilder())
            ->insert('afup_audit_log')
            ->setValue('message', ':message')
            ->setParameter('message', $message)
        ;

        if ($userId) {
            $query->setValue('user_id', ':userId');
            $query->setParameter('userId', $userId);
        }

        if ($route) {
            $query->setValue('route', ':route');
            $query->setParameter('route', $route);
        }

        $query->executeStatement();
    }

    public function paginate(int $page)
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('afup_audit_log')
            ->setMaxResults(10)
            ->setFirstResult(($page - 1) * 10);

        return $query->fetchAllAssociative();
    }
}
