<?php

declare(strict_types=1);

namespace AppBundle\Tests\Doctrine\Type;

use AppBundle\Antennes\ReseauxSociaux;
use AppBundle\Doctrine\Type\AntenneReseauxSociauxType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;

final class AntenneReseauxSociauxTypeTest extends TestCase
{
    public function testRoundTrip(): void
    {
        $type = new AntenneReseauxSociauxType();
        $platform = new MySQLPlatform();

        $socials = new ReseauxSociaux(
            youtube: 'https://www.youtube.com/@afuplyon',
            blog: 'https://lyon.afup.org',
            twitter: 'AFUP_Lyon',
            linkedin: 'afup-lyon',
            bluesky: 'lyon.afup.org',
        );

        $json = $type->convertToDatabaseValue($socials, $platform);
        self::assertSame(
            '{"youtube":"https://www.youtube.com/@afuplyon","blog":"https://lyon.afup.org","twitter":"AFUP_Lyon","linkedin":"afup-lyon","bluesky":"lyon.afup.org"}',
            $json,
        );

        self::assertEquals($socials, $type->convertToPHPValue($json, $platform));
    }

    public function testEmptySocials(): void
    {
        $type = new AntenneReseauxSociauxType();
        $platform = new MySQLPlatform();

        self::assertSame('{}', $type->convertToDatabaseValue(new ReseauxSociaux(), $platform));
        self::assertEquals(new ReseauxSociaux(), $type->convertToPHPValue('{}', $platform));
        self::assertEquals(new ReseauxSociaux(), $type->convertToPHPValue('[]', $platform));
    }

    public function testInvalidData(): void
    {
        $this->expectException(ConversionException::class);

        new AntenneReseauxSociauxType()->convertToPHPValue('{"twitter": 42}', new MySQLPlatform());
    }
}
