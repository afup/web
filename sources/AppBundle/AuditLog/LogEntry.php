<?php

declare(strict_types=1);

namespace AppBundle\AuditLog;

final readonly class LogEntry
{
    public function __construct(
        public int $id,
        public \DateTimeImmutable $date,
        public string $texte,
        public ?string $route,
        public ?int $idPersonnePhysique,
        public ?string $nom,
        public ?string $prenom,
    ) {}
}
