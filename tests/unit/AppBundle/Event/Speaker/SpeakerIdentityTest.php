<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Speaker;

use AppBundle\Event\Speaker\SpeakerIdentity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SpeakerIdentityTest extends TestCase
{
    #[DataProvider('initials')]
    public function testGetInitial(string $firstname, string $lastname, string $expected): void
    {
        $identity = new SpeakerIdentity('slug', $firstname, $lastname, [1], new \DateTimeImmutable());

        self::assertSame($expected, $identity->getInitial());
    }

    public static function initials(): \Generator
    {
        yield 'initiale du nom en majuscule' => ['Geoffrey', 'bachelet', 'B'];
        yield 'accent retiré' => ['Élodie', 'Évrard', 'E'];
        yield 'espaces en tête' => ['Cyrille', '  Grandval', 'G'];
        yield 'prénom quand le nom est vide' => ['Ödile', '', 'O'];
        yield 'chiffre' => ['Team', '42', '#'];
        yield 'nom et prénom vides' => ['', '', '#'];
    }
}
