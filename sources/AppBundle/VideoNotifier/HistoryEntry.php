<?php

declare(strict_types=1);

namespace AppBundle\VideoNotifier;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistoryEntryRepository::class)
 * @ORM\Table(name="video_notifier_history")
 */
class HistoryEntry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=false, name="talk_id")
     */
    private int $talkId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $statusIdBluesky = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $statusIdMastodon = null;

    public function __construct(int $talkId)
    {
        $this->talkId = $talkId;
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
