<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

use AppBundle\Event\Model\Speaker;
use AppBundle\VideoNotifier\HistoryEntry;
use MyCLabs\Enum\Enum;
use function Symfony\Component\String\u;

/**
 * @extends Enum<SocialNetwork::*>
 * @method static SocialNetwork Bluesky()
 * @method static SocialNetwork Mastodon()
 */
final class SocialNetwork extends Enum
{
    private const Bluesky = 'bluesky';
    private const Mastodon = 'mastodon';

    public function statusMaxLength(): int
    {
        switch ($this->value) {
            case self::Bluesky:
                return 300;
            case self::Mastodon:
                return 500;
            default:
                return 280;
        }
    }

    public function getSpeakerHandle(Speaker $speaker): ?string
    {
        if ($this->value === self::Bluesky) {
            $handle = $speaker->getBluesky();
        } else {
            $handle = $speaker->getMastodon();
        }

        if ($handle === null) {
            return null;
        }

        return u($handle)->ensureStart('@')->toString();
    }

    public function setStatusId(HistoryEntry $historyEntry, StatusId $statusId): void
    {
        if ($this->value === self::Bluesky) {
            $historyEntry->setStatusIdBluesky($statusId->value);
            return;
        }

        $historyEntry->setStatusIdMastodon($statusId->value);
    }
}
