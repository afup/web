<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork\Bluesky;

use Symfony\Component\String\AbstractString;
use Symfony\Component\String\ByteString;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Cette classe contient des fonctions récupérées depuis le package symfony/bluesky-notifier
 *
 * Ce package n'est disponible qu'à partir de Symfony 7.1 et PHP 8.2
 *
 * @see https://github.com/symfony/bluesky-notifier/blob/7.2/BlueskyTransport.php
 */
final readonly class BlueskyPolyfill
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function parseFacets(string $input): array
    {
        $facets = [];
        $text = new ByteString($input);

        // regex based on: https://bluesky.com/specs/handle#handle-identifier-syntax
        $regex = '#[$|\W](@([a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)#';
        foreach ($this->getMatchAndPosition($text, $regex) as $match) {
            $did = null;

            try {
                $response = $this->httpClient->request('GET', '/xrpc/com.atproto.identity.resolveHandle', [
                    'query' => [
                        'handle' => ltrim((string) $match['match'], '@'),
                    ],
                ]);

                if ($response->getStatusCode() !== 200) {
                    continue;
                }

                $did = $response->toArray()['did'] ?? null;
            } catch (ExceptionInterface) {
                continue;
            }

            if (null === $did) {
                continue;
            }

            $facets[] = [
                'index' => [
                    'byteStart' => $match['start'],
                    'byteEnd' => $match['end'],
                ],
                'features' => [
                    [
                        '$type' => 'app.bsky.richtext.facet#mention',
                        'did' => $did,
                    ],
                ],
            ];
        }

        return $facets;
    }

    private function getMatchAndPosition(AbstractString $text, string $regex): array
    {
        $output = [];
        $handled = [];
        $matches = $text->match($regex, \PREG_PATTERN_ORDER);
        if ([] === $matches) {
            return $output;
        }

        $length = $text->length();
        foreach ($matches[1] as $match) {
            if (isset($handled[$match])) {
                continue;
            }
            $handled[$match] = true;
            $end = -1;
            while (null !== $start = $text->indexOf($match, min($length, $end + 1))) {
                $output[] = [
                    'start' => $start,
                    'end' => $end = $start + (new ByteString($match))->length(),
                    'match' => $match,
                ];
            }
        }

        return $output;
    }
}
