<?php

declare(strict_types=1);

namespace AppBundle\Joindin;

use AppBundle\Event\Model\Talk;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class JoindinComments
{
    public function __construct(
        #[Autowire('@http_client.joindin')]
        private HttpClientInterface $httpClient,
        #[Autowire('@cache.system')]
        private CacheItemPoolInterface $cache,
    ) {}

    /**
     * @return array{comment: mixed, user_display_name: mixed, created_date: mixed, rating: mixed}[]
     */
    public function getCommentsFromTalk(Talk $talk): array
    {
        $joindinId = $talk->getJoindinId();

        if ($joindinId === null) {
            return [];
        }

        try {
            return $this->prepareCommentsFromJoindinResponse($this->callJoindInApi($joindinId));
        } catch (\RuntimeException|ExceptionInterface) {
            return [];
        }
    }

    /**
     * @throws ExceptionInterface
     */
    private function callJoindInApi(int $joindinId): string
    {
        $cacheItem = $this->cache->getItem('joindin_talk_comments_' . $joindinId);

        $cached = $cacheItem->get();

        if ($cacheItem->isHit() && is_string($cached)) {
            return $cached;
        }

        $content = $this->httpClient->request('GET', '/v2.1/talks/' . $joindinId . '/comments', [
            'query' => ['resultsperpage' => 0],
        ])->getContent();

        // Only cache successful responses, so that a joind.in outage is not cached.
        $cacheItem->set($content);
        $this->cache->save($cacheItem);

        return $content;
    }

    /**
     * @return array{comment: mixed, user_display_name: mixed, created_date: mixed, rating: mixed}[]
     */
    private function prepareCommentsFromJoindinResponse(string $response): array
    {
        $decodedResponse = json_decode($response, true);
        if (!is_array($decodedResponse) || !isset($decodedResponse['comments']) || !is_array($decodedResponse['comments'])) {
            throw new \RuntimeException('Error reading joindin response');
        }

        $comments = [];
        foreach ($decodedResponse['comments'] as $comment) {
            if (!is_array($comment)) {
                continue;
            }

            $userDisplayName = $comment['user_display_name'] ?? '';

            $comments[] = [
                'comment' => $comment['comment'] ?? null,
                'user_display_name' => is_scalar($userDisplayName) && (string) $userDisplayName !== '' ? $userDisplayName : 'Anonyme',
                'created_date' => $comment['created_date'] ?? null,
                'rating' => $comment['rating'] ?? null,
            ];
        }

        return $comments;
    }
}
