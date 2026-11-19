<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Talk;

/**
 * Données structurées schema.org VideoObject de la page d'un talk.
 *
 * @see https://developers.google.com/search/docs/appearance/structured-data/video
 */
final readonly class VideoObjectJsonLd
{
    /**
     * Options d'encodage à utiliser pour insérer le JSON dans un <script> :
     * JSON_HEX_TAG encode < et >, si bien qu'un texte contenant « </script> » ne peut pas fermer la balise.
     */
    public const int JSON_FLAGS = JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;

    /**
     * @param array<array{text: mixed}> $transcript les phrases renvoyées par AppBundle\Subtitles\Parser
     * @param string $url URL absolue de la page du talk
     *
     * @return array<string, string>|null null quand le talk n'a pas de vidéo ou pas de date de créneau
     */
    public function getData(Talk $talk, ?\DateTimeInterface $start, array $transcript, string $url): ?array
    {
        if (!$talk->hasYoutubeId() || null === $start) {
            return null;
        }

        $youtubeId = (string) $talk->getYoutubeId();

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $talk->getTitle(),
        ];

        // description est obligatoire pour Google : sans résumé, on reprend le titre.
        $description = $this->toPlainText((string) $talk->getDescription());
        $data['description'] = '' !== $description ? $description : $talk->getTitle();

        $data += [
            'thumbnailUrl' => sprintf('https://img.youtube.com/vi/%s/hqdefault.jpg', $youtubeId),
            'uploadDate' => $start->format(\DateTimeInterface::ATOM),
            'embedUrl' => sprintf('https://www.youtube.com/embed/%s', $youtubeId),
            'url' => $url,
        ];

        if (null !== $talk->getLanguageCode()) {
            $data['inLanguage'] = $talk->getLanguageCode();
        }

        $transcriptText = $this->toPlainText(implode(' ', array_map(
            static fn(array $cue): string => is_string($cue['text']) ? $cue['text'] : '',
            $transcript,
        )));
        if ('' !== $transcriptText) {
            $data['transcript'] = $transcriptText;
        }

        return $data;
    }

    private function toPlainText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }
}
