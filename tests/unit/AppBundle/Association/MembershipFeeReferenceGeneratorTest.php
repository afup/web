<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association;

use AppBundle\Association\MembershipFeeReferenceGenerator;
use AppBundle\Association\MemberType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MembershipFeeReferenceGeneratorTest extends TestCase
{
    #[DataProvider('generateDateProvider')]
    public function testGenerate(\DateTimeImmutable $currentDate, MemberType $typePersonne, int $idPersonne, string $nom, string $expected): void
    {
        $generator = new MembershipFeeReferenceGenerator();

        $actual = $generator->generate($currentDate, $typePersonne, $idPersonne, $nom);

        self::assertEquals($expected, $actual);
    }

    public static function generateDateProvider(): array
    {
        return [
            'Cas général' => [
                'currentDate' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'typePersonne' => MemberType::MemberPhysical,
                'idPersonne' => 1234,
                'nom' => 'DUPONT',
                'expected' => "C2018-020320182020-0-1234-DUPON-875",
            ],
            'Accent en cinquième position' => [
                'currentDate' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'typePersonne' => MemberType::MemberPhysical,
                'idPersonne' => 1234,
                'nom' => 'Jirsé',
                'expected' => "C2018-020320182020-0-1234-JIRSE-A91",
            ],
        ];
    }
}
