<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Repository\TalkRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class LatestVideos
{
    public const int LIMIT = 6;
    private const string CACHE_KEY = 'latest_videos';

    public function __construct(
        private TalkRepository $talkRepository,
        private ClockInterface $clock,
        // Durée de vie du cache définie sur le pool (config/packages/cache.yaml).
        #[Autowire('@cache.latest_videos')]
        private CacheItemPoolInterface $cache,
    ) {}

    /**
     * @return list<LatestVideo>
     */
    public function get(): array
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            $videos = [];
            foreach ($this->talkRepository->getLatestPublishedWithVideo($this->clock->now(), self::LIMIT) as $row) {
                $videos[] = new LatestVideo(
                    (int) $row['talk']->getId(),
                    $row['talk']->getSlug(),
                    $row['talk']->getTitle(),
                    (string) $row['talk']->getYoutubeId(),
                    (string) $row['event']->getTitle(),
                );
            }

            $cacheItem->set($videos);
            $this->cache->save($cacheItem);
        }

        /** @var list<LatestVideo> $videos */
        $videos = $cacheItem->get();

        return $videos;
    }
}
