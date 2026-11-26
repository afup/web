<?php

declare(strict_types=1);

namespace AppBundle\Tests\Twig;

use AppBundle\Twig\FrenchContractionExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FrenchContractionExtensionTest extends TestCase
{
    #[DataProvider('names')]
    public function testContraction(string $name, string $expectedAu, string $expectedDu): void
    {
        $extension = new FrenchContractionExtension();

        self::assertSame($expectedAu, $extension->au($name));
        self::assertSame($expectedDu, $extension->du($name));
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function names(): array
    {
        return [
            'Forum PHP' => ['Forum PHP 2024', 'au Forum PHP 2024', 'du Forum PHP 2024'],
            'PHP Tour' => ['PHP Tour Lyon 2014', 'au PHP Tour Lyon 2014', 'du PHP Tour Lyon 2014'],
            'AFUP Day, élision' => ['AFUP Day 2024 Lyon', "à l'AFUP Day 2024 Lyon", "de l'AFUP Day 2024 Lyon"],
            'voyelle minuscule' => ['apéro PHP', "à l'apéro PHP", "de l'apéro PHP"],
            'voyelle accentuée' => ['Événement en ligne', "à l'Événement en ligne", "de l'Événement en ligne"],
            'h non élidé' => ['Hackathon', 'au Hackathon', 'du Hackathon'],
        ];
    }
}
