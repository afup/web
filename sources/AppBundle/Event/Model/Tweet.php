<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Tweet implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var string
     */
    private $id;

    /**
     * @var int
     */
    private $talkId;

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
        $id = (string) $id;
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
     */
    public function setTalkId($talkId)
    {
        $talkId = (int) $talkId;
        $this->propertyChanged('talkId', $this->talkId, $talkId);
        $this->talkId = $talkId;
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
}
