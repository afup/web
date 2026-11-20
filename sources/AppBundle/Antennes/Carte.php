<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Carte
{
    public function __construct(
        public bool $utiliserSecondeCouleur,
        public AncrageLegende $ancrageLegende,
        public Ville $premiereVille,
        public ?Ville $secondeVille = null,
    ) {}
}
