<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Préposition « à » ou « de » suivie de l'article « le », contractée ou élidée selon le nom qui suit :
 * « au Forum PHP 2024 », « du PHP Tour Lyon 2014 », mais « à l'AFUP Day 2024 Lyon », « de l'AFUP Day 2024 Lyon ».
 *
 * Les noms d'événements de l'AFUP sont masculins ; seule leur initiale décide de l'élision.
 */
final class FrenchContractionExtension extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('au', $this->au(...)),
            new TwigFunction('du', $this->du(...)),
        ];
    }

    public function au(string $name): string
    {
        return ($this->elides($name) ? "à l'" : 'au ') . $name;
    }

    public function du(string $name): string
    {
        return ($this->elides($name) ? "de l'" : 'du ') . $name;
    }

    /**
     * Élision devant une voyelle. Le « h » n'est pas traité : muet ou aspiré, il ne se devine pas.
     */
    private function elides(string $name): bool
    {
        return preg_match('/^[aeiouyàâäéèêëîïôöùûü]/iu', $name) === 1;
    }
}
