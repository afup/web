<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OfficesExtension extends AbstractExtension
{
    public function __construct(private readonly AntenneRepository $antennesRepository) {}

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('office_name', fn(string $code): string => $this->antennesRepository->findByCode($code)->libelle),
            new TwigFunction('office_logo', fn(string $code): string => $this->antennesRepository->findByCode($code)->urlLogo),
            // Toutes les antennes n'ont pas de groupe Meetup (ex : Clermont), d'où le `?->`
            new TwigFunction('office_meetup_urlname', fn(string $code): ?string => $this->antennesRepository->findByCode($code)->meetup?->nomUrl),
        ];
    }
}
