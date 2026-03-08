<?php

declare(strict_types=1);

namespace AppBundle\Compta\BankAccount;

final readonly class BankAccount
{
    public function __construct(
        public string $etablissement,
        public string $guichet,
        public string $compte,
        public string $cle,
        public string $domicialisation,
        public string $bic,
        public string $iban,
    ) {}
}
