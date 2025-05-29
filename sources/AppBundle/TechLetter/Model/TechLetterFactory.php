<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;

final readonly class TechLetterFactory
{
    public function __construct(
        private MapperBuilder $mapperBuilder,
    ) {}

    public function createTechLetterFromJson(?string $json): TechLetter
    {
        if ($json === null) {
            return new TechLetter();
        }

        return $this->mapperBuilder
            ->supportDateFormats('!Y-m-d')
            ->mapper()
            ->map(TechLetter::class, Source::json($json));
    }
}
