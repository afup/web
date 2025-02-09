<?php

declare(strict_types=1);

namespace AppBundle\Model;

class ComptaModeReglement
{
    const CB = 2;
    const VIREMENT = 3;
    const CHEQUE = 4;
    const PRELEVEMENT = 5;

    public static function list(): array
    {
        return [
            self::CB => 'Carte bancaire',
            self::VIREMENT => 'Virement',
            self::CHEQUE => 'Chèque',
            self::PRELEVEMENT => 'Prélèvement',
        ];
    }
}
