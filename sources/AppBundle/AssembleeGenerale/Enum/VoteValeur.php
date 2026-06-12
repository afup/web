<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Enum;

enum VoteValeur: string
{
    case Oui = 'oui';
    case Non = 'non';
    case Abstention = 'abstention';

    public function label(): string
    {
        return match ($this) {
            self::Oui => 'Oui',
            self::Non => 'Non',
            self::Abstention => 'Abstention',
        };
    }
}
