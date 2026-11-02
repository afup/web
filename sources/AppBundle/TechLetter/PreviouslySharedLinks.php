<?php

declare(strict_types=1);

namespace AppBundle\TechLetter;

use AppBundle\TechLetter\Model\TechLetter;
use AppBundle\TechLetter\Model\TechLetterFactory;
use AppBundle\Veille\Entity\Envoi;
use AppBundle\Veille\Entity\Repository\EnvoiRepository;

final readonly class PreviouslySharedLinks
{
    public function __construct(
        private EnvoiRepository $envoiRepository,
        private TechLetterFactory $techLetterFactory,
    ) {}

    /**
     * Find the links of the given techletter that were already shared in a techletter sent to Mailchimp.
     *
     * @return array<string, list<\DateTimeInterface>> url => sending dates of the previous techletters, most recent first
     */
    public function findIn(TechLetter $techLetter, Envoi $current): array
    {
        $urlsByNormalizedUrl = [];
        foreach ($techLetter->getUrls() as $url) {
            $urlsByNormalizedUrl[self::normalizeUrl($url)][] = $url;
        }

        if ($urlsByNormalizedUrl === []) {
            return [];
        }

        $sharedLinks = [];

        /** @var list<Envoi> $previousEnvois */
        $previousEnvois = $this->envoiRepository->findBy(['envoyeMailchimp' => true], ['dateEnvoi' => 'DESC']);

        foreach ($previousEnvois as $previousEnvoi) {
            if ($previousEnvoi->id === $current->id) {
                continue;
            }

            $previousUrls = $this->techLetterFactory->createTechLetterFromJson($previousEnvoi->contenu)->getUrls();

            foreach (array_unique(array_map(self::normalizeUrl(...), $previousUrls)) as $normalizedUrl) {
                foreach ($urlsByNormalizedUrl[$normalizedUrl] ?? [] as $url) {
                    $sharedLinks[$url][] = $previousEnvoi->dateEnvoi;
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
}
