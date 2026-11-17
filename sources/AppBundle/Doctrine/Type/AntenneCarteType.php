<?php

declare(strict_types=1);

namespace AppBundle\Doctrine\Type;

use AppBundle\Antennes\Carte;
use AppBundle\Antennes\Ville;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;

/**
 * Colonne JSON `afup_antenne.carte` hydratée en objet Carte (et ses Ville / Point / Position).
 *
 * Format stocké :
 * {
 *   "utiliserSecondeCouleur": false,
 *   "ancrageLegende": "right",
 *   "premiereVille": {
 *     "premierPoint": {"x": 330, "y": 440},
 *     "deuxiemePoint": {"x": 270, "y": 500},
 *     "troisiemePoint": {"x": 230, "y": 500},
 *     "position": {"latitude": 44.837912, "longitude": -0.579541}
 *   },
 *   "secondeVille": null
 * }
 *
 * @extends JsonValueObjectType<Carte>
 */
final class AntenneCarteType extends JsonValueObjectType
{
    public const string NAME = 'antenne_carte';

    /**
     * Un type Doctrine ne pouvant pas recevoir de service injecté, le mapper est construit une seule fois ici.
     */
    private static ?TreeMapper $mapper = null;

    public function getName(): string
    {
        return self::NAME;
    }

    protected function valueClass(): string
    {
        return Carte::class;
    }

    protected function fromArray(array $data): Carte
    {
        try {
            return self::mapper()->map(Carte::class, $data);
        } catch (MappingError $e) {
            throw new \UnexpectedValueException($e->getMessage(), 0, $e);
        }
    }

    protected function toArray(object $value): array
    {
        return [
            'utiliserSecondeCouleur' => $value->utiliserSecondeCouleur,
            'ancrageLegende' => $value->ancrageLegende->value,
            'premiereVille' => self::villeToArray($value->premiereVille),
            'secondeVille' => $value->secondeVille !== null ? self::villeToArray($value->secondeVille) : null,
        ];
    }

    private static function mapper(): TreeMapper
    {
        return self::$mapper ??= new MapperBuilder()->mapper();
    }

    /**
     * @return array<string, mixed>
     */
    private static function villeToArray(Ville $ville): array
    {
        return [
            'premierPoint' => ['x' => $ville->premierPoint->x, 'y' => $ville->premierPoint->y],
            'deuxiemePoint' => ['x' => $ville->deuxiemePoint->x, 'y' => $ville->deuxiemePoint->y],
            'troisiemePoint' => ['x' => $ville->troisiemePoint->x, 'y' => $ville->troisiemePoint->y],
            'position' => ['latitude' => $ville->position->latitude, 'longitude' => $ville->position->longitude],
        ];
    }
}
