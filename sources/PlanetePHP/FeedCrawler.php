<?php

declare(strict_types=1);

namespace PlanetePHP;

use Afup\Site\Logger\DbLoggerTrait;

class FeedCrawler
{
    use DbLoggerTrait;

    private FeedRepository $feedRepository;
    private FeedArticleRepository $feedArticleRepository;

    public function __construct(
        FeedRepository $feedRepository,
        FeedArticleRepository $feedArticleRepository
    ) {
        $this->feedRepository = $feedRepository;
        $this->feedArticleRepository = $feedArticleRepository;
        define('MAGPIE_CACHE_DIR', __DIR__ . '/../../var/cache/prod/planete');
        define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
        require_once __DIR__ . '/../../dependencies/magpierss/rss_fetch.inc';
    }

    public function crawl(): void
    {
        $startMicrotime = microtime(true);
        $billets = $success = 0;
        $feeds = $this->feedRepository->findActive();
        foreach ($feeds as $feed) {
            echo $feed->getFeed() . ' : début...<br />', PHP_EOL;
            $rss = fetch_rss($feed->getFeed());
            if (!$rss->items) {
                echo $feed->getFeed(), ' : vide fin !<br /><br/>', PHP_EOL, PHP_EOL;
                continue;
            }
            $rss->items = array_reverse($rss->items);
            foreach ($rss->items as $item) {
                if (empty($item['id'])) {
                    $item['id'] = $item['link'];
                }
                if (empty($item['atom_content'])) {
                    $item['atom_content'] = $item['summary'];
                }
                if (empty($item['atom_content'])) {
                    $item['atom_content'] = $item['content'];
                }
                if ($item['atom_content'] === "A") {
                    $item['atom_content'] = $item['description'];
                }
                if (empty($item['updated']) && isset($item['dc']['date'])) {
                    $item['updated'] = $item['dc']['date'];
                }
                if (empty($item['updated']) && isset($item['modified'])) {
                    $item['updated'] = $item['modified'];
                }
                if (empty($item['updated']) && isset($item['pubdate'])) {
                    $item['updated'] = $item['pubdate'];
                }
                if (empty($item['author'])) {
                    $item['author'] = $feed->getName();
                }

                $item['timestamp'] = strtotime($item['updated']);
                if ($item['timestamp'] > time() - 7 * 24 * 3600) {
                    echo sprintf(' - contenu récent : "%s"', $item['title']), PHP_EOL;
                    $contenu = $item['title'] . " " . $item['atom_content'];
                    $item['etat'] = $this->feedArticleRepository->isRelevant($contenu);
                    $success += $this->feedArticleRepository->save(new FeedArticle(
                        null,
                        $feed->getId(),
                        $item['id'],
                        $item['title'],
                        $item['link'],
                        $item['timestamp'],
                        $item['author'],
                        $item['summary'],
                        $item['atom_content'],
                        $item['etat']
                    ));
                    $billets++;
                }
            }
            echo $feed->getFeed(), ' : fin !<br /><br/>', PHP_EOL, PHP_EOL;
        }
        $errors = $billets - $success;
        $duration = round(microtime(true) - $startMicrotime, 2);
        $this->log(sprintf('Exploration de %s flux -- %d erreur(s) -- en %ss', count($feeds), $errors, $duration));
    }
}
