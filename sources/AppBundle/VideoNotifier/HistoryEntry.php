<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

class HistoryEntry
{
    private ?string $statusIdBluesky = null;
    private ?string $statusIdMastodon = null;

    public function __construct(private readonly int $talkId)
    {
    }

    public function setStatusIdBluesky(?string $statusIdBluesky): void
    {
        $this->statusIdBluesky = $statusIdBluesky;
    }

    public function setStatusIdMastodon(?string $statusIdMastodon): void
    {
        $this->statusIdMastodon = $statusIdMastodon;
    }

    public function getTalkId(): int
    {
        return $this->talkId;
    }

    public function getStatusIdBluesky(): ?string
    {
        return $this->statusIdBluesky;
    }

    public function getStatusIdMastodon(): ?string
    {
        return $this->statusIdMastodon;
    }
}
