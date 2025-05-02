<?php

declare(strict_types=1);

namespace AppBundle\SocialNetwork;

use AppBundle\Event\Model\Speaker;
use AppBundle\VideoNotifier\HistoryEntry;
use function Symfony\Component\String\u;

enum SocialNetwork: string
{
    case Bluesky = 'bluesky';
    case Mastodon = 'mastodon';

    public function statusMaxLength(): int
    {
        return match ($this) {
            self::Bluesky => 300,
            self::Mastodon => 500,
        };
    }

    public function getSpeakerHandle(Speaker $speaker): ?string
    {
        if ($this === self::Bluesky) {
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
        if ($this === self::Bluesky) {
            $historyEntry->setStatusIdBluesky($statusId->value);
            return;
        }

        $historyEntry->setStatusIdMastodon($statusId->value);
    }
}
