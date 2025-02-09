<?php

declare(strict_types=1);

namespace Afup\Site\Utils\tests\units;

use Afup\Site\Utils\Utils as UtilsToTest;

class Utils extends \atoum
{
    protected function dataProvider()
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
    public function testCryptFromText($decrypted, $encrypted): void
    {
        $this
            ->string(UtilsToTest::cryptFromText($decrypted))
            ->isEqualTo($encrypted);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDecryptFromText($decrypted, $encrypted): void
    {
        $this
            ->string(UtilsToTest::decryptFromText($encrypted))
            ->isEqualTo($decrypted);
    }
}
