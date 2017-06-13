<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class TicketType implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $technicalName;

    /**
     * @var string
     */
    private $prettyName;

    /**
     * @var bool
     */
    private $isPublic;

    /**
     * @var bool
     */
    private $isRestrictedToMembers;

    /**
     * @var float
     */
    private $defaultPrice;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string
     */
    private $day;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TicketType
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTechnicalName()
    {
        return $this->technicalName;
    }

    /**
     * @param string $technicalName
     * @return TicketType
     */
    public function setTechnicalName($technicalName)
    {
        $this->propertyChanged('technicalName', $this->technicalName, $technicalName);
        $this->technicalName = $technicalName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrettyName()
    {
        return $this->prettyName;
    }

    /**
     * @param string $prettyName
     * @return TicketType
     */
    public function setPrettyName($prettyName)
    {
        $this->propertyChanged('prettyName', $this->prettyName, $prettyName);
        $this->prettyName = $prettyName;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     * @return TicketType
     */
    public function setIsPublic($isPublic)
    {
        $this->propertyChanged('isPublic', $this->isPublic, $isPublic);
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRestrictedToMembers()
    {
        return $this->isRestrictedToMembers;
    }

    /**
     * @param bool $isRestrictedToMembers
     * @return TicketType
     */
    public function setIsRestrictedToMembers($isRestrictedToMembers)
    {
        $this->propertyChanged('isRestrictedToMembers', $this->isRestrictedToMembers, $isRestrictedToMembers);
        $this->isRestrictedToMembers = $isRestrictedToMembers;
        return $this;
    }

    /**
     * @return float
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }

    /**
     * @param float $defaultPrice
     * @return TicketType
     */
    public function setDefaultPrice($defaultPrice)
    {
        $this->propertyChanged('defaultPrice', $this->defaultPrice, $defaultPrice);
        $this->defaultPrice = $defaultPrice;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return TicketType
     */
    public function setIsActive($isActive)
    {
        $this->propertyChanged('isActive', $this->isActive, $isActive);
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param string $day
     * @return TicketType
     */
    public function setDay($day)
    {
        $this->propertyChanged('day', $this->day, $day);
        $this->day = $day;
        return $this;
    }

    public function getDays()
    {
        return explode(',', $this->day);
    }
}
