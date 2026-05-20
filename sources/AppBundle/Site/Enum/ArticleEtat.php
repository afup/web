<?php

declare(strict_types=1);

namespace AppBundle\Site\Enum;

enum ArticleEtat: int
{
    case HorsLigne = -1;
    case EnAttente = 0;
    case EnLigne = 1;

    public function label(): string
    {
        return match ($this) {
            self::HorsLigne => 'Hors ligne',
            self::EnAttente => 'En attente',
            self::EnLigne => 'En ligne',
        };
    }
}
