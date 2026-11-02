<?php

declare(strict_types=1);

namespace AppBundle\Tests\TechLetter;

use AppBundle\TechLetter\Model\Article;
use AppBundle\TechLetter\Model\News;
use AppBundle\TechLetter\Model\Project;
use AppBundle\TechLetter\Model\TechLetter;
use AppBundle\TechLetter\Model\TechLetterFactory;
use AppBundle\TechLetter\PreviouslySharedLinks;
use AppBundle\Veille\Entity\Envoi;
use AppBundle\Veille\Entity\Repository\EnvoiRepository;
use CuyZ\Valinor\MapperBuilder;
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
        $current = $this->envoi(3, '2026-11-04', null, false);

        $repository = $this->createMock(EnvoiRepository::class);
        $repository
            ->expects(self::once())
            ->method('findBy')
            ->with(['envoyeMailchimp' => true], ['dateEnvoi' => 'DESC'])
            ->willReturn([
                $this->envoi(2, '2026-10-21', new TechLetter(
                    firstNews: new News('https://www.example.com/news/', 'News', new \DateTimeImmutable('2026-10-20')),
                    articles: [
                        $this->article('https://example.com/article'),
                        $this->article('https://example.com/article#duplicate-in-same-techletter'),
                    ],
                ), true),
                $this->envoi(1, '2026-10-08', new TechLetter(
                    articles: [$this->article('https://example.com/article')],
                    projects: [new Project('https://github.com/afup/web', 'afup/web', 'Le site')],
                ), true),
            ]);

        $techLetter = new TechLetter(
            firstNews: new News('https://example.com/news', 'News', new \DateTimeImmutable('2026-11-02')),
            secondNews: new News('https://example.com/new-news', 'New news', new \DateTimeImmutable('2026-11-03')),
            articles: [$this->article('http://example.com/article/')],
            projects: [new Project('https://github.com/afup/web', 'afup/web', 'Le site')],
        );

        $sharedLinks = new PreviouslySharedLinks($repository, new TechLetterFactory(new MapperBuilder()))
            ->findIn($techLetter, $current);

        self::assertEquals([
            'https://example.com/news' => [new \DateTime('2026-10-21')],
            'http://example.com/article/' => [new \DateTime('2026-10-21'), new \DateTime('2026-10-08')],
            'https://github.com/afup/web' => [new \DateTime('2026-10-08')],
        ], $sharedLinks);
    }

    public function testFindInEmptyTechLetterDoesNotQueryTheDatabase(): void
    {
        $repository = $this->createMock(EnvoiRepository::class);
        $repository->expects(self::never())->method('findBy');

        $sharedLinks = new PreviouslySharedLinks($repository, new TechLetterFactory(new MapperBuilder()))
            ->findIn(new TechLetter(), $this->envoi(1, '2026-11-04', null, false));

        self::assertSame([], $sharedLinks);
    }

    private function envoi(int $id, string $date, ?TechLetter $techLetter, bool $sent): Envoi
    {
        $envoi = new Envoi();
        $envoi->id = $id;
        $envoi->dateEnvoi = new \DateTime($date);
        $envoi->contenu = $techLetter instanceof TechLetter ? json_encode($techLetter->jsonSerialize(), JSON_THROW_ON_ERROR) : null;
        $envoi->envoyeMailchimp = $sent;

        return $envoi;
    }

    private function article(string $url): Article
    {
        return new Article($url, 'Title', 'example.com', '5', 'Excerpt', 'en');
    }
}
