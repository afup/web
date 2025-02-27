<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\SocialNetwork\Embed;
use AppBundle\SocialNetwork\SocialNetwork;
use AppBundle\SocialNetwork\Status;

final class StatusGenerator
{
    private SocialNetwork $socialNetwork;

    public function __construct(SocialNetwork $socialNetwork)
    {
        $this->socialNetwork = $socialNetwork;
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
            throw new \InvalidArgumentException('no speaker provided');
        }

        $mentionsText = $this->buildMentionsText($speakers);

        $text = sprintf(
            "« %s » La conférence de %s à revoir sur le site de l'AFUP",
            $talk->getTitle(),
            $mentionsText,
        );

        // Si c'est trop long, on remplace "La conférence de" par "Par"
        if (mb_strlen($text) > $this->socialNetwork->statusMaxLength()) {
            $text = sprintf(
                "« %s » Par %s à revoir sur le site de l'AFUP",
                $talk->getTitle(),
                $mentionsText,
            );
        }

        // Si c'est encore trop long, on enlève "le site"
        if (mb_strlen($text) > $this->socialNetwork->statusMaxLength()) {
            $text = sprintf(
                "« %s » Par %s à revoir sur l'AFUP",
                $talk->getTitle(),
                $mentionsText,
            );
        }

        // Remplacement des espaces multiples par un seul
        $text = preg_replace('/\s+/', ' ', $text);

        if (($length = mb_strlen($text)) > $this->socialNetwork->statusMaxLength()) {
            throw new \LengthException(sprintf(
                "Taille du status %s (%d/%d) incorrecte : %s",
                $this->socialNetwork->getValue(),
                $length,
                $this->socialNetwork->statusMaxLength(),
                $text,
            ));
        }

        return new Status(
            $text,
            new Embed(
                'https://afup.org/talks/' . $talk->getId() . '-' . $talk->getSlug(),
                $talk->getTitle(),
                $talk->getAbstract(),
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
            fn (Speaker $speaker) => $this->socialNetwork->speakerHandle($speaker) ?? $speaker->getLabel(),
            $speakers,
        );

        $count = count($mentions);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $mentions[0];
        }

        if ($count === 2) {
            return $mentions[0] . ' et ' . $mentions[1];
        }

        $lastMention = array_pop($mentions);

        return implode(', ', $mentions) . ' et ' . $lastMention;
    }

    private function buildThumbnailUrl(Talk $talk): ?string
    {
        $youTubeId = $talk->getYoutubeId();

        if ($youTubeId === null) {
            return null;
        }

        return sprintf('https://i.ytimg.com/vi_webp/%s/maxresdefault.webp', $youTubeId);
    }
}
