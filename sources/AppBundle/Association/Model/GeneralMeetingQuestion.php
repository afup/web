<?php

namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class GeneralMeetingQuestion implements NotifyPropertyInterface
{
    use NotifyProperty;

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
     * @param \DateTime $submittedOn
     *
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
     * @param \DateTime $submittedOn
     *
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
}
