<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\Antennes\AntennesCollection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OfficesExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('office_name', fn ($code): string => (new AntennesCollection())->findByCode($code)->label),
            new TwigFunction('office_logo', fn ($code): string => (new AntennesCollection())->findByCode($code)->logoUrl),
            new TwigFunction('office_meetup_urlname', fn ($code) => (new AntennesCollection())->findByCode($code)->meetup->urlName),
        ];
    }
}
