<?php

declare(strict_types=1);

namespace PlanetePHP;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum FeedStatus: int implements TranslatableInterface
{
    case Inactive = 0;
    case Active = 1;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Inactive => 'Inactif',
            self::Active => 'Actif',
        };
    }
}
