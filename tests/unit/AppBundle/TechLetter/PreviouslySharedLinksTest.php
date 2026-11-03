<?php

declare(strict_types=1);

namespace AppBundle\Tests\TechLetter;

use AppBundle\TechLetter\Model\Article;
use AppBundle\TechLetter\Model\News;
use AppBundle\TechLetter\Model\Project;
use AppBundle\TechLetter\Model\TechLetter;
use AppBundle\TechLetter\PreviouslySharedLinks;
use AppBundle\Veille\Entity\Repository\EnvoiRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PreviouslySharedLinksTest extends TestCase
{
    #[DataProvider('equivalentUrlsProvider')]
    public function testNormalizeUrlIgnoresCosmeticDifferences(string $url, string $equivalentUrl): void
    {
        self::assertSame(
            PreviouslySharedLinks::normalizeUrl($url),
            PreviouslySharedLinks::normalizeUrl($equivalentUrl),
        );
    }

    public static function equivalentUrlsProvider(): \Generator
    {
        yield 'scheme' => ['http://afup.org/blog', 'https://afup.org/blog'];
        yield 'www' => ['https://www.afup.org/blog', 'https://afup.org/blog'];
        yield 'trailing slash' => ['https://afup.org/blog/', 'https://afup.org/blog'];
        yield 'fragment' => ['https://afup.org/blog#comments', 'https://afup.org/blog'];
        yield 'case' => ['https://AFUP.org/Blog', 'https://afup.org/blog'];
        yield 'whitespace' => [' https://afup.org/blog ', 'https://afup.org/blog'];
    }

    public function testNormalizeUrlKeepsQueryString(): void
    {
        self::assertNotSame(
            PreviouslySharedLinks::normalizeUrl('https://afup.org/blog?page=1'),
            PreviouslySharedLinks::normalizeUrl('https://afup.org/blog?page=2'),
        );
    }

    public function testFindIn(): void
    {
        $repository = $this->createMock(EnvoiRepository::class);
        $repository
            ->expects(self::once())
            ->method('getSentContentsOrderedByDateDesc')
            ->willReturn([
                $this->sent('2026-10-21', new TechLetter(
                    firstNews: new News('https://www.example.com/news/', 'News', new \DateTimeImmutable('2026-10-20')),
                    articles: [
                        $this->article('https://example.com/article'),
                        $this->article('https://example.com/article#duplicate-in-same-techletter'),
                    ],
                )),
                $this->sent('2026-10-08', new TechLetter(
                    articles: [$this->article('https://example.com/article')],
                    projects: [new Project('https://github.com/afup/web', 'afup/web', 'Le site')],
                )),
            ]);

        $techLetter = new TechLetter(
            firstNews: new News('https://example.com/news', 'News', new \DateTimeImmutable('2026-11-02')),
            secondNews: new News('https://example.com/new-news', 'New news', new \DateTimeImmutable('2026-11-03')),
            articles: [$this->article('http://example.com/article/')],
            projects: [new Project('https://github.com/afup/web', 'afup/web', 'Le site')],
        );

        self::assertEquals([
            'https://example.com/news' => [new \DateTime('2026-10-21')],
            'http://example.com/article/' => [new \DateTime('2026-10-21'), new \DateTime('2026-10-08')],
            'https://github.com/afup/web' => [new \DateTime('2026-10-08')],
        ], new PreviouslySharedLinks($repository)->findIn($techLetter));
    }

    public function testFindInIgnoresUnexpectedContents(): void
    {
        $repository = $this->createStub(EnvoiRepository::class);
        $repository
            ->method('getSentContentsOrderedByDateDesc')
            ->willReturn([
                ['dateEnvoi' => new \DateTime('2026-10-21'), 'contenu' => null],
                ['dateEnvoi' => new \DateTime('2026-10-20'), 'contenu' => '{not json'],
                ['dateEnvoi' => new \DateTime('2026-10-19'), 'contenu' => '"a string"'],
                ['dateEnvoi' => new \DateTime('2026-10-18'), 'contenu' => '{"firstNews":"oops","articles":"oops","projects":[{"name":"no url"},{"url":42}]}'],
                ['dateEnvoi' => new \DateTime('2026-10-17'), 'contenu' => '{"projects":[{"url":"https:\\/\\/example.com\\/article"}]}'],
            ]);

        self::assertEquals(
            ['https://example.com/article' => [new \DateTime('2026-10-17')]],
            new PreviouslySharedLinks($repository)->findIn(new TechLetter(articles: [$this->article('https://example.com/article')])),
        );
    }

    public function testFindInEmptyTechLetterDoesNotQueryTheDatabase(): void
    {
        $repository = $this->createMock(EnvoiRepository::class);
        $repository->expects(self::never())->method('getSentContentsOrderedByDateDesc');

        self::assertSame([], new PreviouslySharedLinks($repository)->findIn(new TechLetter()));
    }

    /**
     * @return array{dateEnvoi: \DateTimeInterface, contenu: string}
     */
    private function sent(string $date, TechLetter $techLetter): array
    {
        return [
            'dateEnvoi' => new \DateTime($date),
            'contenu' => json_encode($techLetter->jsonSerialize(), JSON_THROW_ON_ERROR),
        ];
    }

    private function article(string $url): Article
    {
        return new Article($url, 'Title', 'example.com', '5', 'Excerpt', 'en');
    }
}
