<?php

declare(strict_types=1);

namespace AppBundle\Slack;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Client de l'API Web Slack pour l'espace de travail "Slack Membres".
 */
final readonly class MembresClient
{
    public const string POST_MESSAGE_API = '/chat.postMessage';

    public function __construct(
        #[Autowire(env: 'SLACK_MEMBRE_TOKEN')]
        private string $token,
        #[Autowire(env: 'SLACK_API_URL')]
        private string $apiBaseUrl,
        private HttpClientInterface $httpClient,
    ) {}

    /**
     * @param list<array<string, mixed>> $blocks
     *
     * @throws \RuntimeException
     */
    public function postMessage(string $channelId, string $text, array $blocks = []): void
    {
        $payload = [
            'channel' => $channelId,
            'text' => $text,
            'unfurl_links' => false,
            'unfurl_media' => false,
        ];

        if ($blocks !== []) {
            $payload['blocks'] = $blocks;
        }

        $response = $this->httpClient->request('POST', $this->apiBaseUrl . self::POST_MESSAGE_API, [
            'auth_bearer' => $this->token,
            'json' => $payload,
        ]);

        /** @var array{ok?: bool, error?: string} $content */
        $content = $response->toArray();

        if (($content['ok'] ?? false) !== true) {
            throw new \RuntimeException(sprintf(
                "Erreur lors de l'envoi du message Slack sur le canal %s : %s",
                $channelId,
                $content['error'] ?? 'réponse inattendue',
            ));
        }
    }
}
