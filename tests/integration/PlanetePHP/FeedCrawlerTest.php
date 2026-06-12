<?php

declare(strict_types=1);

namespace PlanetePHP\IntegrationTests;

use Afup\Tests\Support\IntegrationTestCase;
use PlanetePHP\Article;
use PlanetePHP\ArticleRepository;
use PlanetePHP\Feed;
use PlanetePHP\FeedCrawler;
use PlanetePHP\FeedRepository;
use PlanetePHP\FeedStatus;
use PlanetePHP\SymfonyFeedClient;
use Psr\Log\NullLogger;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class FeedCrawlerTest extends IntegrationTestCase
{
    public function testCrawlFeed(): void
    {
        $articlesRepository = self::getContainer()->get(ArticleRepository::class);
        $feedRepository = self::getContainer()->get(FeedRepository::class);

        // L'auto-incrément démarre à 148 dans le script de création de la bdd
        $this->createFeed($feedRepository, 'fake rss', 'https://rss.fake');
        $this->createFeed($feedRepository, 'fake atom', 'https://atom.fake');
        $this->createFeed($feedRepository, 'fake invalid', 'https://invalid.fake');
        $this->createFeed($feedRepository, 'fake html', 'https://html.fake');
        $this->createFeed($feedRepository, 'fake error 500', 'https://error500.fake');
        $this->createFeed($feedRepository, 'fake error 404', 'https://error404.fake');
        $this->createFeed($feedRepository, 'fake empty', 'https://empty.fake');

        $client = new MockHttpClient(function (string $method, string $url): MockResponse {
            if ($url === 'https://atom.fake/') {
                return new MockResponse(file_get_contents(__DIR__ . '/../fixtures/feed-atom.xml'));
            }

            if ($url === 'https://rss.fake/') {
                return new MockResponse(file_get_contents(__DIR__ . '/../fixtures/feed-rss.xml'));
            }

            if ($url === 'https://invalid.fake/') {
                return new MockResponse('invalid xml');
            }

            if ($url === 'https://html.fake/') {
                return new MockResponse(file_get_contents(__DIR__ . '/../fixtures/feed-html.html'));
            }

            if ($url === 'https://error500.fake/') {
                return new MockResponse('', ['http_code' => 500]);
            }

            if ($url === 'https://error404.fake/') {
                return new MockResponse('', ['http_code' => 404]);
            }

            return new MockResponse();
        });

        $crawler = new FeedCrawler(
            new MockClock(\DateTimeImmutable::createFromFormat('Y-m-d', '2025-03-18')),
            new SymfonyFeedClient($client),
            $feedRepository,
            $articlesRepository,
            new NullLogger(),
        );

        $result = $crawler->crawl();

        self::assertEquals(5, $result->saved);
        self::assertEquals([154, 153, 152, 151, 150], $result->failedFeedsIds);
        self::assertEquals(1, $result->tooOld);

        self::assertEquals(5, $articlesRepository->count());
        self::assertEquals(3, $articlesRepository->countRelevant());

        $articles = $articlesRepository->findLatest();

        self::assertCount(3, $articles);

        self::assertInstanceOf(Article::class, $articles[0]);
        self::assertEquals('fake rss', $articles[0]->feed->name);
        self::assertEquals("Lorem Ipsum 🐘", $articles[0]->title);
        self::assertEquals("https://rss.fake/news/123-lorem-ipsum", $articles[0]->url);
        self::assertNull($articles[0]->author);
        self::assertEquals('2025-03-17T07:15:12+01:00', $articles[0]->updatedAt->format(DATE_ATOM));
        self::assertEquals("<h3>Test php</h3>\n<p>salut</p>", $articles[0]->content);

        self::assertInstanceOf(Article::class, $articles[1]);
        self::assertEquals('fake atom', $articles[1]->feed->name);
        self::assertEquals("Lorem Ipsum", $articles[1]->title);
        self::assertEquals("https://atom.fake/lorem-ipsum", $articles[1]->url);
        self::assertEquals('Ada Lovelace', $articles[1]->author);
        self::assertEquals('2025-03-16T14:15:00+01:00', $articles[1]->updatedAt->format(DATE_ATOM));
        self::assertEquals('<p>Contenu PHP</p>', $articles[1]->content);

        self::assertInstanceOf(Article::class, $articles[2]);
        self::assertEquals('fake rss', $articles[2]->feed->name);
        self::assertEquals("Autre article", $articles[2]->title);
        self::assertEquals("https://rss.fake/news/456-autre-article", $articles[2]->url);
        self::assertNull($articles[2]->author);
        self::assertEquals('2025-03-14T22:26:02+01:00', $articles[2]->updatedAt->format(DATE_ATOM));
        self::assertEquals('<p>contenu php</p>', $articles[2]->content);
    }

    private function createFeed(FeedRepository $feedRepository, string $name, string $url): void
    {
        $feed = new Feed();
        $feed->name = $name;
        $feed->url = $url;
        $feed->feed = $url;
        $feed->status = FeedStatus::Active;
        $feedRepository->save($feed);
    }
}
