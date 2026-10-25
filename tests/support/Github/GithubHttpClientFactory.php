<?php

declare(strict_types=1);

namespace Afup\Tests\Support\Github;

use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Remplace l'API GitHub en test : les appels réels, non authentifiés, sont
 * limités à 60 requêtes par heure et par IP et faisaient échouer la CI.
 */
final readonly class GithubHttpClientFactory
{
    private const array USERS = [
        'amenophis' => [
            'login' => 'amenophis',
            'id' => 2158235,
            'avatar_url' => 'https://avatars.githubusercontent.com/u/2158235?v=4',
            'company' => '@yousign',
            'name' => 'Jérémy Leherpeur',
            'html_url' => 'https://github.com/amenophis',
        ],
        'vinceAmstoutz' => [
            'login' => 'vinceAmstoutz',
            'id' => 46444652,
            'avatar_url' => 'https://avatars.githubusercontent.com/u/46444652?v=4',
            'company' => 'Swikly',
            'name' => 'Vincent Amstoutz',
            'html_url' => 'https://github.com/vinceAmstoutz',
        ],
    ];

    public function __invoke(): HttpClientInterface
    {
        return new MockHttpClient(function (string $method, string $url): MockResponse {
            $username = substr((string) parse_url($url, PHP_URL_PATH), strlen('/users/'));

            if (isset(self::USERS[$username])) {
                return new JsonMockResponse(self::USERS[$username]);
            }

            return new JsonMockResponse(['message' => 'Not Found'], ['http_code' => 404]);
        }, 'https://api.github.com');
    }
}
