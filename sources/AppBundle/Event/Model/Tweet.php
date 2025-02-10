<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Tweet implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?string $id = null;

    private ?int $talkId = null;

    private ?\DateTime $createdAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $id = (string) $id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;

        return $this;
    }

    public function getTalkId(): ?int
    {
        return $this->talkId;
    }

    public function setTalkId($talkId): void
    {
        $talkId = (int) $talkId;
        $this->propertyChanged('talkId', $this->talkId, $talkId);
        $this->talkId = $talkId;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->propertyChanged('createdAt', $this->createdAt, $createdAt);
        $this->createdAt = $createdAt;

        return $this;
    }
}
