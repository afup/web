<?php

declare(strict_types=1);

namespace AppBundle\Tests\Text;

use AppBundle\Text\Truncator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TruncatorTest extends TestCase
{
    #[DataProvider('texts')]
    public function testTruncate(string $text, int $maxLength, string $expected): void
    {
        $result = Truncator::truncate($text, $maxLength);

        self::assertSame($expected, $result);
        self::assertLessThanOrEqual($maxLength, mb_strlen($result));
    }

    public static function texts(): \Generator
    {
        yield 'texte à la limite : inchangé' => ['Une conférence', 14, 'Une conférence'];
        yield 'coupe dans un mot : on recule au mot précédent' => ['Une conférence', 10, 'Une…'];
        yield 'coupe juste avant un espace : le dernier mot est gardé' => ['Une conférence sur PHP', 15, 'Une conférence…'];
        yield 'ponctuation retirée avant « … »' => ['Une conférence, puis la suite', 20, 'Une conférence…'];
        yield 'tiret retiré avant « … »' => ['Une conférence – puis la suite', 20, 'Une conférence…'];
        yield 'mot unique trop long : coupe franche' => [str_repeat('é', 20), 10, str_repeat('é', 9) . '…'];
        // « ē » s'écrit C4 93 en UTF-8 ; 0x93 est aussi le dernier octet de « – ».
        yield 'caractère multi-octet en fin de coupe conservé' => ['Talk sur Dē et la suite', 12, 'Talk sur Dē…'];
    }
}
