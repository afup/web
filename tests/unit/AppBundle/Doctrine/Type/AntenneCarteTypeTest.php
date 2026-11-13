<?php

declare(strict_types=1);

namespace AppBundle\Tests\Doctrine\Type;

use AppBundle\Antennes\AncrageLegende;
use AppBundle\Antennes\Carte;
use AppBundle\Antennes\Point;
use AppBundle\Antennes\Position;
use AppBundle\Antennes\Ville;
use AppBundle\Doctrine\Type\AntenneCarteType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;

final class AntenneCarteTypeTest extends TestCase
{
    public function testRoundTrip(): void
    {
        $type = new AntenneCarteType();
        $platform = new MySQLPlatform();

        $carte = new Carte(
            utiliserSecondeCouleur: true,
            ancrageLegende: AncrageLegende::Gauche,
            premiereVille: new Ville(
                new Point(630, 190),
                new Point(680, 250),
                new Point(720, 250),
                new Position(48.6921, 6.1844),
            ),
            secondeVille: new Ville(
                new Point(630, 140),
                new Point(680, 200),
                new Point(720, 200),
                new Position(47.380001068115234, -0.579541),
            ),
        );

        $json = $type->convertToDatabaseValue($carte, $platform);
        self::assertIsString($json);

        self::assertEquals($carte, $type->convertToPHPValue($json, $platform));
    }

    public function testSansSecondeVilleEtCoordonneesEntieres(): void
    {
        $type = new AntenneCarteType();

        $carte = $type->convertToPHPValue(
            '{"utiliserSecondeCouleur": false, "ancrageLegende": "top", "premiereVille": {"premierPoint": {"x": 1, "y": 2}, "deuxiemePoint": {"x": 3, "y": 4}, "troisiemePoint": {"x": 5, "y": 6}, "position": {"latitude": 45, "longitude": 1.25}}}',
            new MySQLPlatform(),
        );

        self::assertInstanceOf(Carte::class, $carte);
        self::assertSame(AncrageLegende::Haut, $carte->ancrageLegende);
        self::assertNull($carte->secondeVille);
        self::assertSame(45.0, $carte->premiereVille->position->latitude);
        self::assertSame(5, $carte->premiereVille->troisiemePoint->x);
    }

    public function testNull(): void
    {
        $type = new AntenneCarteType();

        self::assertNull($type->convertToPHPValue(null, new MySQLPlatform()));
        self::assertNull($type->convertToDatabaseValue(null, new MySQLPlatform()));
    }

    public function testInvalidData(): void
    {
        $this->expectException(ConversionException::class);

        new AntenneCarteType()->convertToPHPValue('{"utiliserSecondeCouleur": false, "ancrageLegende": "diagonal"}', new MySQLPlatform());
    }
}
