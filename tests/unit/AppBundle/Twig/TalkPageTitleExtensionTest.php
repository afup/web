<?php

declare(strict_types=1);

namespace AppBundle\Tests\Twig;

use AppBundle\Twig\TalkPageTitleExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TalkPageTitleExtensionTest extends TestCase
{
    #[DataProvider('titles')]
    public function testTalkPageTitle(string $talkTitle, string $eventTitle, string $expected): void
    {
        $title = new TalkPageTitleExtension()->talkPageTitle($talkTitle, $eventTitle);

        self::assertSame($expected, $title);
        self::assertLessThanOrEqual(TalkPageTitleExtension::MAX_LENGTH, mb_strlen($title));
    }

    public static function titles(): \Generator
    {
        yield 'titre court : avec l\'événement' => [
            'Jouons tous ensemble à un petit jeu',
            'Forum PHP 2019',
            'Jouons tous ensemble à un petit jeu – Forum PHP 2019',
        ];

        // 49 + 3 + 13 = 65 caractères, accents compris
        yield 'exactement 65 caractères avec l\'événement' => [
            'Révolutionons PHP : les évolutions à venir en été',
            'AFUP Day 2024',
            'Révolutionons PHP : les évolutions à venir en été – AFUP Day 2024',
        ];

        yield 'un caractère de trop avec l\'événement : titre seul' => [
            'Révolutionons PHP : les évolutions à venir en étés',
            'AFUP Day 2024',
            'Révolutionons PHP : les évolutions à venir en étés',
        ];

        yield 'titre seul de 65 caractères : non tronqué' => [
            'REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform',
            'Forum PHP 2019',
            'REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform',
        ];

        yield 'titre trop long : tronqué sur une frontière de mot' => [
            'REST ou GraphQL ? Exemples illustrés avec Symfony et API Platformé',
            'Forum PHP 2019',
            'REST ou GraphQL ? Exemples illustrés avec Symfony et API…',
        ];

        yield 'la coupe tombe juste avant un espace : le dernier mot est gardé' => [
            'Ééééé ééééé ééééé ééééé ééééé ééééé ééééé ééééé ééééé ééééé éééé ééééé',
            'Forum PHP 2019',
            'Ééééé ééééé ééééé ééééé ééééé ééééé ééééé ééééé ééééé ééééé éééé…',
        ];

        yield 'ponctuation retirée avant les points de suspension' => [
            'Un titre vraiment très long pour un talk, avec une virgule : puis la suite',
            'Forum PHP 2019',
            'Un titre vraiment très long pour un talk, avec une virgule…',
        ];

        yield 'mot unique trop long : coupe franche' => [
            str_repeat('é', 80),
            'Forum PHP 2019',
            str_repeat('é', 64) . '…',
        ];

        yield 'espaces en bordure ignorés' => [
            '  Jouons tous ensemble  ',
            ' forum ',
            'Jouons tous ensemble – forum',
        ];
    }
}
