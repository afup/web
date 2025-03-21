<?php

declare(strict_types=1);

namespace PlanetePHP\IntegrationTests;

use Afup\Tests\Support\IntegrationTestCase;
use Afup\Tests\Support\MockClock;
use PlanetePHP\DisplayableFeedArticle;
use PlanetePHP\FeedArticleRepository;
use PlanetePHP\FeedCrawler;
use PlanetePHP\FeedRepository;
use PlanetePHP\SymfonyFeedClient;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class FeedCrawlerTest extends IntegrationTestCase
{
    public function testCrawlFeed(): void
    {
        $articlesRepository = self::getContainer()->get(FeedArticleRepository::class);
        $feedRepository = self::getContainer()->get(FeedRepository::class);

        $feedRepository->insert('fake rss', 'https://rss.fake', 'https://rss.fake', 1);
        $feedRepository->insert('fake atom', 'https://atom.fake', 'https://atom.fake', 1);

        $client = new MockHttpClient(function (string $method, string $url): MockResponse {
            if ($url === 'https://atom.fake/') {
                return new MockResponse(file_get_contents(__DIR__ . '/../fixtures/feed-atom.xml'));
            }

            return new MockResponse(file_get_contents(__DIR__ . '/../fixtures/feed-rss.xml'));
        });

        $crawler = new FeedCrawler(
            new MockClock(\DateTimeImmutable::createFromFormat('Y-m-d', '2025-03-18')),
            new SymfonyFeedClient($client),
            $feedRepository,
            $articlesRepository,
            new NullLogger(),
        );

        $crawler->crawl();

        self::assertEquals(5, $articlesRepository->count());
        self::assertEquals(3, $articlesRepository->countRelevant());

        $articles = $articlesRepository->findLatest();

        self::assertCount(3, $articles);

        self::assertInstanceOf(DisplayableFeedArticle::class, $articles[0]);
        self::assertEquals('fake rss', $articles[0]->getFeedName());
        self::assertEquals("Lorem Ipsum 🐘", $articles[0]->getTitle());
        self::assertEquals("https://rss.fake/news/123-lorem-ipsum", $articles[0]->getUrl());
        self::assertNull($articles[0]->getAuthor());
        self::assertEquals('2025-03-17T07:15:12+01:00', $articles[0]->getUpdate());
        self::assertEquals("<h3>Test php</h3>\n<p>salut</p>", $articles[0]->getContent());

        self::assertInstanceOf(DisplayableFeedArticle::class, $articles[1]);
        self::assertEquals('fake atom', $articles[1]->getFeedName());
        self::assertEquals("Lorem Ipsum", $articles[1]->getTitle());
        self::assertEquals("https://atom.fake/lorem-ipsum", $articles[1]->getUrl());
        self::assertEquals('Ada Lovelace', $articles[1]->getAuthor());
        self::assertEquals('2025-03-16T14:15:00+01:00', $articles[1]->getUpdate());
        self::assertEquals('<p>Contenu PHP</p>', $articles[1]->getContent());

        self::assertInstanceOf(DisplayableFeedArticle::class, $articles[2]);
        self::assertEquals('fake rss', $articles[2]->getFeedName());
        self::assertEquals("Autre article", $articles[2]->getTitle());
        self::assertEquals("https://rss.fake/news/456-autre-article", $articles[2]->getUrl());
        self::assertNull($articles[2]->getAuthor());
        self::assertEquals('2025-03-14T22:26:02+01:00', $articles[2]->getUpdate());
        self::assertEquals('<p>contenu php</p>', $articles[2]->getContent());
    }
}
