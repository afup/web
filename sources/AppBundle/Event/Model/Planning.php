<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Planning implements NotifyPropertyInterface
{
    use NotifyProperty;
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $talkId;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $eventId;

    /**
     * @var bool
     */
    private $isKeynote = false;

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
        $id = (int)$id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTalkId()
    {
        return $this->talkId;
    }

    /**
     * @param int $talkId
     *
     * @return $this
     */
    public function setTalkId($talkId)
    {
        $talkId = (int)$talkId;
        $this->propertyChanged('talkId', $this->talkId, $talkId);
        $this->talkId = $talkId;

        return $this;
    }

    /**
     * @return int
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
    public function setEventId($eventId)
    {
        $eventId = (int)$eventId;
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @param int $isKeynote

     * @return $this
     */
    public function setIsKeynote($isKeynote)
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
