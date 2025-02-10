<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class SpeakerSuggestion implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $eventId;

    /**
     * @var string
     */
    protected $suggesterEmail;

    /**
     * @var string
     */
    protected $suggesterName;

    /**
     * @var string
     */
    protected $speakerName;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var \DateTime
     */
    protected $createdAt;

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
    public function setId($id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEventId()
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
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuggesterEmail()
    {
        return $this->suggesterEmail;
    }

    /**
     * @param string $suggesterEmail
     *
     * @return $this
     */
    public function setSuggesterEmail($suggesterEmail): self
    {
        $this->propertyChanged('suggesterEmail', $this->suggesterEmail, $suggesterEmail);
        $this->suggesterEmail = $suggesterEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuggesterName()
    {
        return $this->suggesterName;
    }

    /**
     * @param string $suggesterName
     *
     * @return $this
     */
    public function setSuggesterName($suggesterName): self
    {
        $this->propertyChanged('suggesterName', $this->suggesterName, $suggesterName);
        $this->suggesterName = $suggesterName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSpeakerName()
    {
        return $this->speakerName;
    }

    /**
     * @param string $speakerName
     *
     * @return $this
     */
    public function setSpeakerName($speakerName): self
    {
        $this->propertyChanged('speakerName', $this->speakerName, $speakerName);
        $this->speakerName = $speakerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment): self
    {
        $this->propertyChanged('comment', $this->comment, $comment);
        $this->comment = $comment;

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
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->propertyChanged('createdAt', $this->createdAt, $createdAt);
        $this->createdAt = $createdAt;

        return $this;
    }
}
