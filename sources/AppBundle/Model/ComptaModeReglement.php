<?php

declare(strict_types=1);

namespace AppBundle\Model;

class ComptaModeReglement
{
    public const CB = 2;
    public const VIREMENT = 3;
    public const CHEQUE = 4;
    public const PRELEVEMENT = 5;

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
