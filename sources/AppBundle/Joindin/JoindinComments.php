<?php

namespace AppBundle\Joindin;

use AppBundle\Event\Model\Talk;
use Psr\Cache\CacheItemPoolInterface;

class JoindinComments
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Talk $talk
     *
     * @return array
     */
    public function getCommentsFromTalk(Talk $talk)
    {
        if (!$talk->hasJoindinId()) {
            return [];
        }

        return $this->getCommmentsFromId($talk->getJoindinId());
    }

    /**
     * @param int $joindinId
     *
     * @return array
     */
    protected function getCommmentsFromId($joindinId)
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
     * @return mixed
     *
     * @throws \Exception
     */
    private function prepareCommentsFromJoindinResponse($response)
    {
        $decodedResponse = json_decode($response, true);
        if (!is_array($decodedResponse) || !isset($decodedResponse['comments'])) {
            throw new \Exception('Error reading joindin response');
        }

        $comments = [];
        foreach ($decodedResponse['comments'] as $comment) {
            if (0 === strlen($comment['user_display_name'])) {
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
