<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork\Bluesky;

use AppBundle\SocialNetwork\Embed;
use AppBundle\SocialNetwork\SocialNetwork;
use AppBundle\SocialNetwork\Status;
use AppBundle\SocialNetwork\StatusId;
use AppBundle\SocialNetwork\Transport;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BlueskyTransport implements Transport
{
    private HttpClientInterface $httpClient;
    private string $apiIdentifier;
    private string $apiAppPassword;
    private ?Session $session = null;

    public function __construct(HttpClientInterface $httpClient, string $apiIdentifier, string $apiAppPassword)
    {
        $this->httpClient = $httpClient;
        $this->apiIdentifier = $apiIdentifier;
        $this->apiAppPassword = $apiAppPassword;
    }

    public function socialNetwork(): SocialNetwork
    {
        return SocialNetwork::Bluesky();
    }

    public function send(Status $status): ?StatusId
    {
        $record = [
            '$type' => 'app.bsky.feed.post',
            'text' => $status->text,
            'createdAt' => date('Y-m-d\\TH:i:s.u\\Z'),
        ];

        if ($status->embed !== null) {
            $record['embed'] = $this->buildEmbed($status->embed);
        }

        $facets = (new BlueskyPolyfill($this->httpClient))->parseFacets($status->text);

        if ($facets !== []) {
            $record['facets'] = $facets;
        }

        $response = $this->httpClient->request('POST', '/xrpc/com.atproto.repo.createRecord', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session()->accessJwt,
            ],
            'json' => [
                'collection' => 'app.bsky.feed.post',
                'repo' => $this->session()->did,
                'record' => $record,
            ],
        ]);

        $uri = $response->toArray()['uri'] ?? null;

        if ($uri !== null) {
            return new StatusId($this->extractPostId($uri));
        }

        return null;
    }

    private function session(): Session
    {
        if ($this->session === null) {
            $response = $this->httpClient->request('POST', '/xrpc/com.atproto.server.createSession', [
                'json' => [
                    'identifier' => $this->apiIdentifier,
                    'password' => $this->apiAppPassword,
                ],
            ]);

            $this->session = (new MapperBuilder())
                ->allowSuperfluousKeys()
                ->mapper()
                ->map(Session::class, Source::array($response->toArray()));
        }

        return $this->session;
    }

    // Un embed est ce qui permet d'avoir un cadre sous le texte du status avec un titre, une description et
    // éventuellement une image.
    private function buildEmbed(Embed $embedData): array
    {
        $embed = [
            '$type' => 'app.bsky.embed.external',
            'external' => [
                'uri' => $embedData->url,
                'title' => $embedData->title,
                'description' => $embedData->abstract,
            ],
        ];

        if ($embedData->imageUrl !== null) {
            try {
                $thumbnail = $this->buildThumbnail($embedData->imageUrl);
            } catch (Exception $e) {
                // Si une erreur survient, on ne bloque pas la création du post.
                // Il sera juste envoyé sans image.
                return $embed;
            }

            if ($thumbnail !== null) {
                $embed['external']['thumb'] = $thumbnail;
            }
        }

        return $embed;
    }

    /**
     * Cette fonction tente d'uploader une image sur Bluesky pour ajouter au statut.
     * S'il y a la moindre erreur, on ne bloque pas, on génère juste un statut sans image.
     */
    private function buildThumbnail(string $thumbnailUrl): ?array
    {
        $downloadResponse = $this->httpClient->request('GET', $thumbnailUrl);

        if ($downloadResponse->getStatusCode() !== 200) {
            return null;
        }

        $thumbnailBlob = $downloadResponse->getContent();

        $uploadResponse = $this->httpClient->request('POST', '/xrpc/com.atproto.repo.uploadBlob', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session()->accessJwt,
                'Content-Type' => 'image/webp',
                'Content-Length' => mb_strlen($thumbnailBlob),
            ],
            'body' => $thumbnailBlob,
        ]);

        if ($uploadResponse->getStatusCode() !== 200) {
            return null;
        }

        // La clé blob contient un tableau dans le format attendu pour l'ajout du thumbnail
        // dans le status donc pas besoin de le parser, on peut directement faire passe-plat.
        return $uploadResponse->toArray()['blob'] ?? null;
    }

    /**
     * Bluesky ne retourne pas directement un id, mais une url au format "at://..."
     * Cette fonction est chargée d'en extraire l'id du post qui sera enregistré en base.
     */
    private function extractPostId(string $uri): ?string
    {
        if (!preg_match('#^at://([^/]+)/([^/]+)/([^/]+)$#', $uri, $matches)) {
            return null;
        }

        return $matches[3];
    }
}
