<?php

declare(strict_types=1);

namespace AppBundle\AuditLog;

final readonly class LogEntry
{
    public function __construct(
        public int $id,
        public \DateTimeImmutable $createdAt,
        public string $message,
        public ?int $userId,
        public ?string $route,
    ) {}
}
