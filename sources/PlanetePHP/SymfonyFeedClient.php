<?php

declare(strict_types=1);

namespace PlanetePHP;

use Laminas\Feed\Reader\Http\ClientInterface;
use Laminas\Feed\Reader\Http\Response;
use Laminas\Feed\Reader\Http\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SymfonyFeedClient implements ClientInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function get($uri): ResponseInterface
    {
        $response = $this->httpClient->request('GET', $uri);

        return new Response(
            $response->getStatusCode(),
            $response->getContent(),
        );
    }
}
