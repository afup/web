<?php

declare(strict_types=1);

namespace AppBundle\Tests\Twig;

use AppBundle\Twig\MetaDescriptionExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MetaDescriptionExtensionTest extends TestCase
{
    #[DataProvider('contentProvider')]
    public function testMetaDescription(?string $content, string $expected): void
    {
        self::assertSame($expected, new MetaDescriptionExtension()->metaDescription($content));
    }

    public static function contentProvider(): \Generator
    {
        yield 'contenu vide' => [null, ''];
        yield 'texte court inchangé' => ['Une conférence sur PHP.', 'Une conférence sur PHP.'];
        yield 'balises retirées' => ['<p>Une <strong>conférence</strong> sur <a href="https://php.net">PHP</a>.</p>', 'Une conférence sur PHP.'];
        yield 'blocs séparés par une espace' => ["<h2>Titre</h2><p>Premier.</p><ul><li>Un</li><li>Deux</li></ul>Fin<br>ligne", 'Titre Premier. Un Deux Fin ligne'];
        yield 'entités décodées' => ['L&#039;AFUP &amp; la communauté &laquo;&nbsp;PHP&nbsp;&raquo; &eacute;t&eacute;', "L'AFUP & la communauté « PHP » été"];
        yield 'espaces normalisés' => ["  Un\n\n  texte\t indenté  ", 'Un texte indenté'];
    }

    public function testTruncatesOnWordBoundary(): void
    {
        $content = str_repeat('mot ', 35) . 'conférence finale';

        $result = new MetaDescriptionExtension()->metaDescription($content);

        self::assertLessThanOrEqual(MetaDescriptionExtension::MAX_LENGTH, mb_strlen($result));
        self::assertSame(str_repeat('mot ', 35) . 'conférence…', $result);
    }

    public function testExactMaxLengthIsKept(): void
    {
        $content = str_repeat('a', 150) . ' éèàù';

        self::assertSame($content, new MetaDescriptionExtension()->metaDescription($content));
    }

    public function testDoesNotKeepTrailingPunctuationBeforeEllipsis(): void
    {
        $content = str_repeat('a', 145) . ' fin, ' . str_repeat('b', 20);

        self::assertSame(str_repeat('a', 145) . ' fin…', new MetaDescriptionExtension()->metaDescription($content));
    }

    public function testTruncatesSingleLongWord(): void
    {
        $result = new MetaDescriptionExtension()->metaDescription(str_repeat('a', 200));

        self::assertSame(str_repeat('a', 154) . '…', $result);
    }

    public function testCustomLength(): void
    {
        self::assertSame('Une…', new MetaDescriptionExtension()->metaDescription('Une conférence', 10));
    }

    public function testTagsAreStrippedBeforeMeasuring(): void
    {
        $content = '<p class="' . str_repeat('x', 300) . '">Texte court</p>';

        self::assertSame('Texte court', new MetaDescriptionExtension()->metaDescription($content));
    }
}
