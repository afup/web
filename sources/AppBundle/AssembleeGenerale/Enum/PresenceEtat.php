<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Enum;

enum PresenceEtat: int
{
    case EnAttente = 0;
    case Present = 1;
    case NonPresent = 2;

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Present => 'Présent',
            self::NonPresent => 'Non présent',
        };
    }
}
