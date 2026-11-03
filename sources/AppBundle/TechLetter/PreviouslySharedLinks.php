<?php

declare(strict_types=1);

namespace AppBundle\TechLetter;

use AppBundle\TechLetter\Model\TechLetter;
use AppBundle\Veille\Entity\Repository\EnvoiRepository;

final readonly class PreviouslySharedLinks
{
    public function __construct(private EnvoiRepository $envoiRepository) {}

    /**
     * Find the links of the given techletter that were already shared in a techletter sent to Mailchimp.
     *
     * @return array<string, list<\DateTimeInterface>> url => sending dates of the previous techletters, most recent first
     */
    public function findIn(TechLetter $techLetter): array
    {
        $urlsByNormalizedUrl = [];
        foreach ($techLetter->getUrls() as $url) {
            $urlsByNormalizedUrl[self::normalizeUrl($url)][] = $url;
        }

        if ($urlsByNormalizedUrl === []) {
            return [];
        }

        $sharedLinks = [];

        // This runs on every preview refresh: only the URLs are needed, so the raw JSON is decoded
        // instead of mapping every sent techletter to its model (~60x faster with ~250 techletters).
        foreach ($this->envoiRepository->getSentContentsOrderedByDateDesc() as $sent) {
            $previousUrls = array_unique(array_map(self::normalizeUrl(...), self::extractUrls($sent['contenu'])));

            foreach ($previousUrls as $normalizedUrl) {
                foreach ($urlsByNormalizedUrl[$normalizedUrl] ?? [] as $url) {
                    $sharedLinks[$url][] = $sent['dateEnvoi'];
                }
            }
        }

        return $sharedLinks;
    }

    /**
     * Ignore differences that do not change the shared content: scheme, "www.", trailing slash, fragment and case.
     */
    public static function normalizeUrl(string $url): string
    {
        $url = trim($url);
        $url = (string) preg_replace('~#.*$~', '', $url);
        $url = (string) preg_replace('~^https?://(www\.)?~i', '', $url);

        return mb_strtolower(rtrim($url, '/'));
    }

    /**
     * URLs of the news, articles and projects of a techletter stored as JSON (see TechLetter::jsonSerialize()).
     *
     * @return list<string>
     */
    private static function extractUrls(?string $json): array
    {
        $data = json_decode($json ?? '', true);
        if (!is_array($data)) {
            return [];
        }

        $items = [$data['firstNews'] ?? null, $data['secondNews'] ?? null];
        foreach (['articles', 'projects'] as $collection) {
            $collectionItems = $data[$collection] ?? null;
            if (is_array($collectionItems)) {
                array_push($items, ...array_values($collectionItems));
            }
        }

        $urls = [];
        foreach ($items as $item) {
            $url = is_array($item) ? $item['url'] ?? null : null;
            if (is_string($url)) {
                $urls[] = $url;
            }
        }

        return $urls;
    }
}
