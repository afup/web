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

        // L'auto-incrément démarre à 148 dans le script de création de la bdd
        $feedRepository->insert('fake rss', 'https://rss.fake', 'https://rss.fake', 1);
        $feedRepository->insert('fake atom', 'https://atom.fake', 'https://atom.fake', 1);
        $feedRepository->insert('fake invalid', 'https://invalid.fake', 'https://invalid.fake', 1);
        $feedRepository->insert('fake html', 'https://html.fake', 'https://html.fake', 1);
        $feedRepository->insert('fake error 500', 'https://error500.fake', 'https://error500.fake', 1);
        $feedRepository->insert('fake error 404', 'https://error404.fake', 'https://error404.fake', 1);
        $feedRepository->insert('fake empty', 'https://empty.fake', 'https://empty.fake', 1);

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
