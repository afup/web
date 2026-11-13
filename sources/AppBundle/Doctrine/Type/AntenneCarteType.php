<?php

declare(strict_types=1);

namespace AppBundle\Doctrine\Type;

use AppBundle\Antennes\AncrageLegende;
use AppBundle\Antennes\Carte;
use AppBundle\Antennes\Point;
use AppBundle\Antennes\Position;
use AppBundle\Antennes\Ville;

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
        $utiliserSecondeCouleur = $data['utiliserSecondeCouleur'] ?? null;
        if (!is_bool($utiliserSecondeCouleur)) {
            throw new \UnexpectedValueException('la clé "utiliserSecondeCouleur" doit être un booléen');
        }

        $ancrageLegende = AncrageLegende::tryFrom((string) self::nullableString($data, 'ancrageLegende'));
        if ($ancrageLegende === null) {
            throw new \UnexpectedValueException('la clé "ancrageLegende" est invalide');
        }

        return new Carte(
            utiliserSecondeCouleur: $utiliserSecondeCouleur,
            ancrageLegende: $ancrageLegende,
            premiereVille: self::ville(self::arrayValue($data, 'premiereVille')),
            secondeVille: isset($data['secondeVille']) ? self::ville(self::arrayValue($data, 'secondeVille')) : null,
        );
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

    /**
     * @param array<mixed> $data
     */
    private static function ville(array $data): Ville
    {
        $position = self::arrayValue($data, 'position');

        return new Ville(
            self::point(self::arrayValue($data, 'premierPoint')),
            self::point(self::arrayValue($data, 'deuxiemePoint')),
            self::point(self::arrayValue($data, 'troisiemePoint')),
            new Position(
                self::float($position, 'latitude'),
                self::float($position, 'longitude'),
            ),
        );
    }

    /**
     * @param array<mixed> $data
     */
    private static function point(array $data): Point
    {
        $x = $data['x'] ?? null;
        $y = $data['y'] ?? null;

        if (!is_int($x) || !is_int($y)) {
            throw new \UnexpectedValueException('les coordonnées "x" et "y" d\'un point doivent être des entiers');
        }

        return new Point($x, $y);
    }

    /**
     * @param array<mixed> $data
     */
    private static function float(array $data, string $key): float
    {
        $value = $data[$key] ?? null;

        if (!is_float($value) && !is_int($value)) {
            throw new \UnexpectedValueException(sprintf('la clé "%s" doit être un nombre', $key));
        }

        return (float) $value;
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
