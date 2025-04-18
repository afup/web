<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;

class TechLetterFactory
{
    public static function createTechLetterFromJson(?string $json): TechLetter
    {
        if ($json === null) {
            return new TechLetter();
        }

        return (new MapperBuilder())
            ->supportDateFormats('!Y-m-d')
            ->mapper()
            ->map(TechLetter::class, Source::json($json));
    }
}
