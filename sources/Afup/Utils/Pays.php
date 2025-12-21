<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

use AppBundle\Site\Model\Repository\CountryRepository;

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
    public function obtenirPays()
    {
        $result = [];
        foreach ($this->countryRepository->getAllCountries() as $country) {
            $result[$country->getId()] = $country->getName();
        }

        return $result;
    }

    /**
     * Renvoit le nom du pays à partir du code ISO
     */
    public function obtenirNom(string $id): string
    {
        return $this->countryRepository->getOneBy(['id' => $id])->getName();
    }
}
