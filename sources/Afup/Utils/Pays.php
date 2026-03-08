<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

use AppBundle\Site\Entity\Repository\CountryRepository;

/**
 * Classe de gestion des pays
 */
class Pays
{
    public const DEFAULT_ID = 'FR';

    public function __construct(private readonly CountryRepository $countryRepository) {}

    /**
     * Renvoit un tableau associatif des pays avec le code ISO comme clé et le nom comme valeur
     *
     * @return array<string, string>
     */
    public function obtenirPays(): array
    {
        $result = [];
        foreach ($this->countryRepository->getAllSortedByName() as $country) {
            $result[$country->id] = $country->name;
        }

        return $result;
    }

    /**
     * Renvoit le nom du pays à partir du code ISO
     */
    public function obtenirNom(string $id): string
    {
        return $this->countryRepository->find($id)->name;
    }
}
