<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\Antennes\AntenneRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OfficesExtension extends AbstractExtension
{
    public function __construct(
        private readonly AntenneRepository $antennesRepository,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('office_name', fn($code): string => $this->antennesRepository->findByCode($code)->label),
            new TwigFunction('office_logo', fn($code): string => $this->antennesRepository->findByCode($code)->logoUrl),
            new TwigFunction('office_meetup_urlname', fn($code) => $this->antennesRepository->findByCode($code)->meetup->urlName),
        ];
    }
}
