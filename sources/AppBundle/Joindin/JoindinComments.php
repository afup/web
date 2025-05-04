<?php

declare(strict_types=1);

namespace AppBundle\Joindin;

use AppBundle\Event\Model\Talk;
use Psr\Cache\CacheItemPoolInterface;

class JoindinComments
{
    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
    }

    public function getCommentsFromTalk(Talk $talk): array
    {
        if (!$talk->hasJoindinId()) {
            return [];
        }

        try {
            return $this->getCommmentsFromId($talk->getJoindinId());
        } catch (\RuntimeException) {
            return [];
        }
    }

    /**
     * @param int $joindinId
     */
    protected function getCommmentsFromId($joindinId): array
    {
        return $this->prepareCommentsFromJoindinResponse($this->callJoindInApi($joindinId));
    }

    /**
     * @param int $joindinId
     *
     * @return string
     */
    private function callJoindInApi($joindinId)
    {
        $joindInurl = 'http://api.joind.in/v2.1/talks/' . $joindinId . '/comments?resultsperpage=0';
        $cacheItem = $this->cache->getItem(urlencode($joindInurl));
        if (!$cacheItem->isHit()) {
            $cacheItem->set(file_get_contents($joindInurl));
            $this->cache->save($cacheItem);
        }

        return $cacheItem->get();
    }

    /**
     * @param string $response
     *
     * @return array{comment: mixed, user_display_name: mixed, created_date: mixed, rating: mixed}[]
     *
     * @throws \Exception
     */
    private function prepareCommentsFromJoindinResponse($response): array
    {
        $decodedResponse = json_decode($response, true);
        if (!is_array($decodedResponse) || !isset($decodedResponse['comments'])) {
            throw new \RuntimeException('Error reading joindin response');
        }

        $comments = [];
        foreach ($decodedResponse['comments'] as $comment) {
            if ((string) $comment['user_display_name'] === '') {
                $comment['user_display_name'] = 'Anonyme';
            }

            $comments[] = [
                'comment' => $comment['comment'],
                'user_display_name' => $comment['user_display_name'],
                'created_date' => $comment['created_date'],
                'rating' => $comment['rating'],
            ];
        }

        return $comments;
    }
}
