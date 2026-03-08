<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

use AppBundle\Accounting\OperationType;

final readonly class Operation
{
    public function __construct(
        public string $dateEcriture,
        public string $description,
        public float $montant,
        private OperationType $type,
        public string $numeroOperation,
    ) {}

    public function isCredit(): bool
    {
        return $this->type === OperationType::Credit;
    }
}
