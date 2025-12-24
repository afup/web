<?php

declare(strict_types=1);

namespace PlanetePHP;

use Laminas\Feed\Exception\ExceptionInterface;
use Laminas\Feed\Reader\Reader;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

final readonly class FeedCrawler
{
    public function __construct(
        private ClockInterface $clock,
        private SymfonyFeedClient $httpClient,
        private FeedRepository $feedRepository,
        private FeedArticleRepository $feedArticleRepository,
        private LoggerInterface $logger,
    ) {
        Reader::setHttpClient($this->httpClient);
    }

    public function crawl(): CrawlingResult
    {
        $feeds = $this->feedRepository->findActive();

        $saved = 0;
        $tooOld = 0;
        $failedFeedsIds = [];

        foreach ($feeds as $feed) {
            $this->logger->info(sprintf('[planete][%s] Start fetching', $feed->name));

            try {
                $items = Reader::import($feed->feed);
            } catch (ExceptionInterface|ClientExceptionInterface|ServerExceptionInterface $e) {
                $this->logger->error(sprintf('[planete][%s] Error: %s', $feed->name, $e->getMessage()));

                // Si une erreur survient, on passe au flux suivant
                $failedFeedsIds[] = $feed->id;
                continue;
            }

            $this->logger->info(sprintf('[planete][%s] Items: %d', $feed->name, count($items)));

            foreach ($items as $item) {
                $this->logger->info(sprintf('[planete][%s] Item: %s', $feed->name, $item->getTitle()));

                $date = $item->getDateCreated();

                if ($date === null || $date < $this->clock->now()->modify('-7 days')) {
                    $tooOld++;
                    continue;
                }

                $author = $item->getAuthor();

                if (is_array($author)) {
                    $author = $author['name'] ?? null;
                }

                $article = new FeedArticle(
                    null,
                    $feed->id,
                    $item->getId(),
                    $item->getTitle(),
                    $item->getLink(),
                    $date->getTimestamp(),
                    $author,
                    $item->getDescription(),
                    $item->getContent(),
                    $this->feedArticleRepository->isRelevant($item->getTitle() . ' ' . $item->getContent()),
                );

                $this->feedArticleRepository->save($article);

                $saved++;
            }
        }

        $this->logger->info(sprintf(
            '[planete] End fetching. %d saved -- %d too old -- %d errors',
            $saved,
            $tooOld,
            implode(', ', $failedFeedsIds),
        ));

        return new CrawlingResult($saved, $tooOld, $failedFeedsIds);
    }
}
