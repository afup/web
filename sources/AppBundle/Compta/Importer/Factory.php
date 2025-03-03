<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

class Factory
{
    public function create(string $filePath, string $code): Importer
    {
        switch ($code) {
            case CreditMutuel::CODE:
                $importer = new CreditMutuel();
                break;
            case CreditMutuelLivret::CODE:
                $importer = new CreditMutuelLivret();
                break;
            default:
                throw new \InvalidArgumentException("Unknown importer for code '$code'");
        }

        $importer->initialize($filePath);

        return $importer;
    }
}
