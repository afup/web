<?php

namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class GeneralMeetingQuestion implements NotifyPropertyInterface
{
    use NotifyProperty;

    const STATUS_WAITING = 'waiting';
    const STATUS_OPENED = 'opened';
    const STATUS_CLOSED = 'closed';

    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $label;

    /**
     * @var \DateTime
     */
    private $openedAt;

    /**
     * @var \DateTime
     */
    private $closedAt;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     *
     * @return $this
     */
    public function setDate(\DateTimeInterface $date)
    {
        $this->propertyChanged('date', $this->date, $date);
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->propertyChanged('label', $this->label, $label);
        $this->label = $label;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOpenedAt()
    {
        return $this->openedAt;
    }

    /**
     * @return $this
     */
    public function setOpenedAt(\DateTime $openedAt = null)
    {
        $this->propertyChanged('openedAt', $this->openedAt, $openedAt);
        $this->openedAt = $openedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * @return $this
     */
    public function setClosedAt(\DateTime $closedAt = null)
    {
        $this->propertyChanged('closedAt', $this->closedAt, $closedAt);
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->propertyChanged('createdAt', $this->createdAt, $createdAt);
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): string
    {
        if (null !== $this->getClosedAt()) {
            return self::STATUS_CLOSED;
        }

        if (null !== $this->getOpenedAt()) {
            return self::STATUS_OPENED;
        }

        return self::STATUS_WAITING;
    }

    public function hasStatusWaiting(): bool
    {
        return self::STATUS_WAITING === $this->getStatus();
    }

    public function hasStatusOpened(): bool
    {
        return self::STATUS_OPENED === $this->getStatus();
    }

    public function hasStatusClosed(): bool
    {
        return self::STATUS_CLOSED === $this->getStatus();
    }

    /**
     * @param array<string, int> $results
     */
    public function hasVotes(array $results): bool
    {
        return 0 > $results[GeneralMeetingVote::VALUE_YES] + $results[GeneralMeetingVote::VALUE_NO] + $results[GeneralMeetingVote::VALUE_ABSTENTION];
    }
}
