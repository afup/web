<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Model\Speaker;

/**
 * Le profil consolidé d'une personne, à partir de ses lignes speaker de toutes les éditions :
 * la bio, la photo et chaque lien social viennent de la ligne la plus récente où ils sont renseignés.
 */
final readonly class SpeakerProfile
{
    /**
     * @param Speaker $speaker la ligne speaker la plus récente
     * @param Speaker|null $photoSpeaker la ligne speaker la plus récente qui a une photo, à passer à PhotoStorage::getUrl()
     * @param array<'twitter'|'mastodon'|'bluesky'|'linkedin'|'github', string> $socialLinks URL par réseau
     * @param non-empty-list<SpeakerTalk> $talks les talks publiés, du plus récent au plus ancien
     */
    public function __construct(
        public SpeakerIdentity $identity,
        public Speaker $speaker,
        public ?string $biography,
        public ?Speaker $photoSpeaker,
        public array $socialLinks,
        public array $talks,
    ) {}
}
