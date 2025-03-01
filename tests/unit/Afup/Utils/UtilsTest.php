<?php

declare(strict_types=1);

namespace Afup\Site\Tests\Utils;

use Afup\Site\Utils\Utils;
use PHPUnit\Framework\TestCase;

final class UtilsTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [
                'decrypted' => 1,
                'encrypted' => '03bITNI5Ono=',
            ],
            [
                'decrypted' => '1',
                'encrypted' => '03bITNI5Ono=',
            ],
            [
                'decrypted' => '12345',
                'encrypted' => 'EIx0Y/wJQ+I=',
            ],
            [
                'decrypted' => 'abcdef',
                'encrypted' => 'UvM1BUAJ5jQ=',
            ],
            [
                'decrypted' => 'L\'AFUP est trop mortelle !',
                'encrypted' => '6MSKdnJmUMW7YrnxXDe/5mKySbAiO2C9ubfR3NcG/fc=',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCryptFromText($decrypted, string $encrypted): void
    {
        self::assertEquals($encrypted, Utils::cryptFromText($decrypted));
        self::assertEquals($decrypted, Utils::decryptFromText($encrypted));
    }
}
