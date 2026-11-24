<?php

declare(strict_types=1);

namespace AppBundle\Joindin;

use AppBundle\Event\Model\Talk;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class JoindinTalk
{
    public function __construct(
        #[Autowire('@http_client.joindin')]
        private HttpClientInterface $httpClient,
        #[Autowire('@cache.system')]
        private CacheItemPoolInterface $cache,
    ) {}

    public function getStubFromTalk(Talk $talk): ?string
    {
        $joindinId = $talk->getJoindinId();

        if ($joindinId === null) {
            return null;
        }

        try {
            return $this->prepareStubFromJoindinResponse($this->callJoindInApi($joindinId));
        } catch (\RuntimeException|ExceptionInterface) {
            return null;
        }
    }

    /**
     * @throws ExceptionInterface
     */
    private function callJoindInApi(int $joindinId): string
    {
        $cacheItem = $this->cache->getItem('joindin_talk_' . $joindinId);

        $cached = $cacheItem->get();

        if ($cacheItem->isHit() && is_string($cached)) {
            return $cached;
        }

        $content = $this->httpClient->request('GET', '/v2.1/talks/' . $joindinId)->getContent();

        // Only cache successful responses, so that a joind.in outage is not cached.
        $cacheItem->set($content);
        $this->cache->save($cacheItem);

        return $content;
    }

    private function prepareStubFromJoindinResponse(string $response): ?string
    {
        $decodedResponse = json_decode($response, true);
        if (!is_array($decodedResponse) || !is_array($decodedResponse['talks'] ?? null)) {
            throw new \RuntimeException('Error reading joindin response');
        }

        $talk = $decodedResponse['talks'][0] ?? null;
        $stub = is_array($talk) ? $talk['stub'] ?? null : null;

        return is_string($stub) ? $stub : null;
    }
}
