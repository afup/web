<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Enum;

enum QuestionEtat: string
{
    case EnAttente = 'waiting';
    case Ouverte = 'opened';
    case Fermee = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Ouverte => 'Ouverte',
            self::Fermee => 'Fermée',
        };
    }
}
