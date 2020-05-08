<?php

namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class GeneralMeetingResponse implements NotifyPropertyInterface
{
    use NotifyProperty;

    const STATUS_PENDING = 0;
    const STATUS_PRESENT = 1;
    const STATUS_NON_PRESENT = 2;

    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $status = self::STATUS_PENDING;

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
     * @return int
     */
    public function getUserid()
    {
        return $this->userId;
    }

    /**
     * @param int $userid
     *
     * @return $this
     */
    public function setUserId($userid)
    {
        $this->propertyChanged('manager', $this->userId, $userid);
        $this->userId = $userid;

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
     * @param \DateTime $submittedOn
     *
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->propertyChanged('date', $this->date, $date);
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->propertyChanged('status', $this->status, $status);
        $this->status = $status;

        return $this;
    }

    public function isPresent()
    {
        return self::STATUS_PRESENT === $this->getStatus();
    }
}
