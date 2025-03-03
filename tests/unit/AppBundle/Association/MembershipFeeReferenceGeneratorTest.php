<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association;

use AppBundle\Association\MembershipFeeReferenceGenerator;
use PHPUnit\Framework\TestCase;

final class MembershipFeeReferenceGeneratorTest extends TestCase
{
    /**
     * @dataProvider generateDateProvider
     */
    public function testGenerate(string $case, \DateTimeImmutable $currentDate, int $typePersonne, int $idPersonne, string $nom, string $expected): void
    {
        $generator = new MembershipFeeReferenceGenerator();

        $actual = $generator->generate($currentDate, $typePersonne, $idPersonne, $nom);

        self::assertEquals($expected, $actual);
    }

    public function generateDateProvider(): array
    {
        return [
            [
                'case' => 'Cas général',
                'current_date' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'type_personne' => 0,
                'id_personne' => 1234,
                'nom' => 'DUPONT',
                'expected' => "C2018-020320182020-0-1234-DUPON-875",
            ],
            [
                'case' => 'Accent en cinquième position',
                'current_date' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'type_personne' => 0,
                'id_personne' => 1234,
                'nom' => 'Jirsé',
                'expected' => "C2018-020320182020-0-1234-JIRSE-A91",
            ],
        ];
    }
}
