<?php

declare(strict_types=1);

namespace AppBundle\Tests\TechLetter\Model;

use AppBundle\TechLetter\Model\Article;
use AppBundle\TechLetter\Model\News;
use AppBundle\TechLetter\Model\Project;
use AppBundle\TechLetter\Model\TechLetter;
use AppBundle\TechLetter\Model\TechLetterFactory;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;

class TechLetterFactoryTest extends TestCase
{
    /**
     * @dataProvider jsonDatProvider
     */
    public function testCreateTechLetterFromJson(string $jsonFilePath, TechLetter $expectedTechLetter): void
    {
        $json = file_get_contents($jsonFilePath);
        self::assertIsString($json);

        $actualTechLetter = TechLetterFactory::createTechLetterFromJson($json);

        self::assertEquals($expectedTechLetter, $actualTechLetter);
    }

    public function jsonDatProvider(): Generator
    {
        yield 'empty' => [
            'json' => __DIR__ . '/fixtures/empty.json',
            'letter' => new TechLetter(),
        ];

        yield 'keys-present-but-empty-values' => [
            'json' => __DIR__ . '/fixtures/keys-present-but-empty-values.json',
            'letter' => new TechLetter(),
        ];

        yield 'with-only-projects' => [
            'json' => __DIR__ . '/fixtures/with-only-projects.json',
            'letter' => new TechLetter(
                null,
                null,
                [],
                [
                    new Project('https://github.com/afup/planete', 'afup/planete', 'Le code source de planete-php.fr'),
                    new Project('https://github.com/afup/barometre', 'afup/barometre', 'Le code source de barometre.afup.org'),
                ],
            ),
        ];

        yield 'with-only-articles' => [
            'json' => __DIR__ . '/fixtures/with-only-articles.json',
            'letter' => new TechLetter(
                null,
                null,
                [
                    new Article('https://example.com/fr', 'Example en français', 'example.com', '2', 'Lorem ipsum', 'fr'),
                    new Article('https://example.com/en', 'Example with default language', 'example.com', '2', 'Lorem ipsum', 'en'),
                ],
                [],
            ),
        ];

        yield 'with-only-first-news' => [
            'json' => __DIR__ . '/fixtures/with-only-first-news.json',
            'letter' => new TechLetter(
                new News(
                    'https://afup.org/news/1222-forum-php-2024-exceptionnel',
                    'Un Forum PHP 2024 exceptionnel !',
                    DateTimeImmutable::createFromFormat('Y-m-d', '2024-10-21'),
                ),
                null,
                [],
                [],
            ),
        ];

        yield 'with-only-second-news' => [
            'json' => __DIR__ . '/fixtures/with-only-second-news.json',
            'letter' => new TechLetter(
                null,
                new News(
                    'https://afup.org/news/1231-enquete2025-barometre-des-salaires-PHP-ouverte',
                    'L’enquête 2025 du baromètre des salaires en PHP est ouverte',
                    DateTimeImmutable::createFromFormat('Y-m-d', '2025-03-17'),
                ),
                [],
                [],
            ),
        ];

        yield 'full' => [
            'json' => __DIR__ . '/fixtures/full.json',
            'letter' => new TechLetter(
                new News(
                    'https://afup.org/news/1222-forum-php-2024-exceptionnel',
                    'Un Forum PHP 2024 exceptionnel !',
                    DateTimeImmutable::createFromFormat('Y-m-d', '2024-10-21'),
                ),
                new News(
                    'https://afup.org/news/1231-enquete2025-barometre-des-salaires-PHP-ouverte',
                    'L’enquête 2025 du baromètre des salaires en PHP est ouverte',
                    DateTimeImmutable::createFromFormat('Y-m-d', '2025-03-17'),
                ),
                [
                    new Article('https://example.com/fr', 'Example en français', 'example.com', '2', 'Lorem ipsum', 'fr'),
                    new Article('https://example.com/en', 'Example with default language', 'example.com', '2', 'Lorem ipsum', 'en'),
                ],
                [
                    new Project('https://github.com/afup/planete', 'afup/planete', 'Le code source de planete-php.fr'),
                    new Project('https://github.com/afup/barometre', 'afup/barometre', 'Le code source de barometre.afup.org'),
                ],
            ),
        ];
    }
}
