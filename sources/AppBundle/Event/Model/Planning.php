<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Planning implements NotifyPropertyInterface
{
    use NotifyProperty;
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private ?int $talkId = null;

    private ?\DateTime $start = null;

    private ?\DateTime $end = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private ?int $eventId = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private ?int $roomId = null;

    private bool $isKeynote = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $id = (int) $id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    public function getTalkId(): ?int
    {
        return $this->talkId;
    }

    public function setTalkId(int $talkId): self
    {
        $this->propertyChanged('talkId', $this->talkId, $talkId);
        $this->talkId = $talkId;

        return $this;
    }

    public function getStart(): ?\DateTime
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): self
    {
        $this->propertyChanged('start', $this->start, $start);
        $this->start = $start;
        return $this;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(\DateTime $end): self
    {
        $this->propertyChanged('end', $this->end, $end);
        $this->end = $end;
        return $this;
    }

    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    public function setEventId(int $eventId): self
    {
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;

        return $this;
    }

    public function setIsKeynote(bool $isKeynote): self
    {
        $this->propertyChanged('isKeynote', $this->isKeynote, $isKeynote);
        $this->isKeynote = $isKeynote;

        return $this;
    }

    public function getIsKeynote(): bool
    {
        return $this->isKeynote;
    }

    public function setRoomId(?int $roomId): self
    {
        $this->propertyChanged('roomId', $this->roomId, $roomId);
        $this->roomId = $roomId;

        return $this;
    }

    public function getRoomId(): ?int
    {
        return $this->roomId;
    }
}
