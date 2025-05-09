<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

class Factory
{
    public function create(string $filePath, string $code): Importer
    {
        $importer = match ($code) {
            CreditMutuel::CODE => new CreditMutuel(),
            CreditMutuelLivret::CODE => new CreditMutuelLivret(),
            default => throw new \InvalidArgumentException("Unknown importer for code '$code'"),
        };

        $importer->initialize($filePath);

        return $importer;
    }
}
