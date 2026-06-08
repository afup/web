<?php

declare(strict_types=1);

namespace AppBundle\Model;

class ComptaModeReglement
{
    public const int CB = 2;
    public const int VIREMENT = 3;
    public const int CHEQUE = 4;
    public const int PRELEVEMENT = 5;

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
