<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

enum TvaTaux: string
{
    case NonSoumis = 'non-soumis';
    case Taux_5_5 = '5.5';
    case Taux_10 = '10';
    case Taux_20 = '20';

    public function label(): string
    {
        return match ($this) {
            self::NonSoumis => 'Non soumis',
            self::Taux_5_5 => '5,5 %',
            self::Taux_10 => '10 %',
            self::Taux_20 => '20 %',
        };
    }

    public function toNumber(): float
    {
        return match ($this) {
            self::NonSoumis => 0,
            self::Taux_5_5 => 5.5,
            self::Taux_10 => 10,
            self::Taux_20 => 20,
        };
    }
}
