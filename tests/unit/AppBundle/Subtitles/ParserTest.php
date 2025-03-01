<?php

declare(strict_types=1);

namespace AppBundle\Tests\Subtitles;

use AppBundle\Subtitles\Parser;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testPars(string $content, array $expectedResult): void
    {
        $parser = new Parser();

        $actualResult = $parser->parse($content);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function parseDataProvider(): array
    {
        $content = <<<EOF
1
00:00:00,000 --> 00:00:08,650
[Musique]

2
00:00:08,711 --> 00:00:19,960
[Musique]

3
00:00:19,960 --> 00:00:24,252
🎧 On va attaquer la prochaine session.

4
00:00:24,252 --> 00:00:26,760
Tu es déjà là Stéphane impeccable

5
00:00:26,760 --> 00:00:29,817
Pour la prochaine prochaine session
on reçoit Stéphane Hulard

6
00:00:29,817 --> 00:00:31,918
Il baigne dans le web et son écosystème

7
00:00:31,918 --> 00:00:35,600
Il est consultant et formateur indépendant
EOF;

        $expected = [
            [
                'start' => 0,
                'text' => '[Musique] [Musique] 🎧 On va attaquer la prochaine session.',
            ],
            [
                'start' => 24,
                'text' => 'Tu es déjà là Stéphane impeccable Pour la prochaine prochaine session
on reçoit Stéphane Hulard Il baigne dans le web et son écosystème Il est consultant et formateur indépendant',
            ],
        ];

        return [
            'Test simple' => [
                'content' => $content,
                'expected' => $expected,
            ],
        ];
    }
}
