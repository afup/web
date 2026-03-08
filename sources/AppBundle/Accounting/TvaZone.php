<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

enum TvaZone: string
{
    case France = 'france';
    case EuropeanUnion = 'ue';
    case OutsideEuropeanUnion = 'hors_ue';
    case Undefined = '';

    public function getLabel(): string
    {
        return match ($this) {
            self::France => 'France',
            self::EuropeanUnion => 'Union Européenne hors France',
            self::OutsideEuropeanUnion => 'Hors Union Européenne',
            self::Undefined => 'Non définie',
        };
    }
}
