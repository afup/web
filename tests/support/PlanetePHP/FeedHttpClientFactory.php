<?php

declare(strict_types=1);

namespace Afup\Tests\Support\PlanetePHP;

use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class FeedHttpClientFactory
{
    public function __invoke(): HttpClientInterface
    {
        return new MockHttpClient(function (string $method, string $url): MockResponse {
            if ($url === 'https://fake.afup/working-feed.xml') {
                return new MockResponse('<?xml version="1.0" encoding="UTF-8"?><a/>');
            }

            return new MockResponse('', ['http_code' => 500]);
        });
    }
}
