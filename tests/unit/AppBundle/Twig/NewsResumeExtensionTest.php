<?php

declare(strict_types=1);

namespace AppBundle\Tests\Twig;

use AppBundle\Markdown\Markdown;
use AppBundle\Site\Entity\Article;
use AppBundle\Twig\NewsResumeExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NewsResumeExtensionTest extends TestCase
{
    #[DataProvider('articles')]
    public function testResume(?string $chapeau, ?string $contenu, string $expected): void
    {
        $article = new Article();
        $article->chapeau = $chapeau;
        $article->contenu = $contenu;

        self::assertSame($expected, new NewsResumeExtension(new Markdown())->resume($article));
    }

    /**
     * @return array<string, array{?string, ?string, string}>
     */
    public static function articles(): array
    {
        return [
            'chapeau en Markdown' => [
                '*Un peu* de texte **en forme** et [un lien](https://afup.org)',
                'Contenu ignoré',
                'Un peu de texte en forme et un lien',
            ],
            'chapeau HTML avec entités' => [
                "<p>Comme &agrave; chaque &eacute;dition, l'AFUP &amp; ses \"amis\"</p>",
                null,
                "Comme à chaque édition, l'AFUP & ses \"amis\"",
            ],
            'retours à la ligne' => [
                "Un<br>Deux\n\nTrois",
                null,
                'Un Deux Trois',
            ],
            'sans chapeau, contenu en Markdown' => [
                null,
                "### Un titre\n\nDu **contenu** & un [lien](https://afup.org).",
                'Un titre Du contenu & un lien.',
            ],
            'chapeau vide, contenu tronqué sans couper de mot' => [
                '',
                str_repeat('mot ', 49) . 'dernier encore',
                str_repeat('mot ', 49) . 'dernier…',
            ],
            'ni chapeau ni contenu' => [null, null, ''],
        ];
    }
}
