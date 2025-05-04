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

    /**
     * @var bool
     */
    private $isKeynote = false;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id): self
    {
        $id = (int) $id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTalkId(): ?int
    {
        return $this->talkId;
    }

    /**
     * @param int $talkId
     *
     * @return $this
     */
    public function setTalkId($talkId): self
    {
        $talkId = (int) $talkId;
        $this->propertyChanged('talkId', $this->talkId, $talkId);
        $this->talkId = $talkId;

        return $this;
    }

    /**
     * @return \DateTime
     */
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

    /**
     * @return \DateTime
     */
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

    /**
     * @return int
     */
    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     *
     * @return $this
     */
    public function setEventId($eventId): self
    {
        $eventId = (int) $eventId;
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @param bool $isKeynote

     * @return $this
     */
    public function setIsKeynote($isKeynote): self
    {
        $this->propertyChanged('isKeynote', $this->isKeynote, $isKeynote);
        $this->isKeynote = $isKeynote;

        return $this;
    }
    /**
     * @return bool
     */
    public function getIsKeynote()
    {
        return $this->isKeynote;
    }
}
