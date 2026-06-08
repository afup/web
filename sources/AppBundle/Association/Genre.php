<?php

declare(strict_types=1);

namespace AppBundle\Association;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Genre: string implements TranslatableInterface
{
    case Femme = 'femme';
    case Homme = 'homme';
    case NonBinaire = 'non-binaire';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('genre.' . $this->value, locale: $locale);
    }
}
