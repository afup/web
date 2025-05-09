<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\SocialNetwork\Embed;
use AppBundle\SocialNetwork\SocialNetwork;
use AppBundle\SocialNetwork\Status;

final readonly class StatusGenerator
{
    public function __construct(private SocialNetwork $socialNetwork)
    {
    }

    /**
     * Cette fonction génère un statut pour un réseau social (Bluesky ou Mastodon par exemple) à partir d'un talk.
     *
     * La fonction tente plusieurs formats de statut jusqu'à en trouver un qui ne dépasse pas la longueur max
     * du réseau social en cours.
     */
    public function generate(Talk $talk, array $speakers): Status
    {
        if ($speakers === []) {
            throw new \InvalidArgumentException('Aucun speaker pour le talk');
        }

        $mentionsText = $this->buildMentionsText($speakers);

        // Nettoyage des espaces dans le titre
        $title = trim((string) preg_replace('/\s+/', ' ', $talk->getTitle()));

        $text = sprintf(
            "« %s », la conférence de %s à revoir sur le site de l'AFUP",
            $title,
            $mentionsText,
        );

        // Si c'est trop long, on remplace "La conférence de" par "Par"
        if ($this->isTextTooLong($text)) {
            $text = sprintf(
                "« %s », par %s à revoir sur le site de l'AFUP",
                $title,
                $mentionsText,
            );
        }

        // Si c'est encore trop long, on enlève "le site"
        if ($this->isTextTooLong($text)) {
            $text = sprintf(
                "« %s », par %s à revoir sur l'AFUP",
                $title,
                $mentionsText,
            );
        }

        if ($this->isTextTooLong($text)) {
            throw new \LengthException(sprintf(
                "Statut généré pour %s trop long",
                $this->socialNetwork->value,
            ));
        }

        return new Status(
            $text,
            new Embed(
                'https://afup.org/talks/' . $talk->getId() . '-' . $talk->getSlug(),
                $title,
                strip_tags(html_entity_decode($talk->getAbstract())),
                $this->buildThumbnailUrl($talk),
            ),
        );
    }

    /**
     * Prend une liste de speakers et les retourne séparés par des virgules et le mot "et".
     *
     * La fonction s'adapte à la quantité de speakers :
     *
     * - ['Foo', 'Bar'] devient "Foo, Bar"
     * - ['Foo', 'Bar', 'Fiz'] devient "Foo, Bar et Fiz"
     *
     * @param array<Speaker> $speakers
     */
    private function buildMentionsText(array $speakers): string
    {
        $mentions = array_map(
            fn (Speaker $speaker): string => $this->socialNetwork->getSpeakerHandle($speaker) ?? $speaker->getLabel(),
            $speakers,
        );

        $count = count($mentions);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $mentions[0];
        }

        $lastMention = array_pop($mentions);

        return implode(', ', $mentions) . ' et ' . $lastMention;
    }

    private function buildThumbnailUrl(Talk $talk): ?string
    {
        $youtubeId = $talk->getYoutubeId();

        if ($youtubeId === null) {
            return null;
        }

        return sprintf('https://i.ytimg.com/vi_webp/%s/maxresdefault.webp', $youtubeId);
    }

    private function isTextTooLong(string $text): bool
    {
        return mb_strlen($text) > $this->socialNetwork->statusMaxLength();
    }
}
