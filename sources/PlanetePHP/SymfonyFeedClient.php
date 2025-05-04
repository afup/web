<?php

declare(strict_types=1);

namespace PlanetePHP;

use Laminas\Feed\Reader\Http\ClientInterface;
use Laminas\Feed\Reader\Http\Response;
use Laminas\Feed\Reader\Http\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class SymfonyFeedClient implements ClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
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
