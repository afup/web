<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class TicketEventType implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $ticketTypeId;

    /**
     * @var int
     */
    private $eventId;

    /**
     * @var float
     */
    private $price;

    /**
     * @var \DateTime
     */
    private $dateStart;

    /**
     * @var \DateTime
     */
    private $dateEnd;

    /**
     * @var string
     */
    private $description;

    /**
     * @var TicketType
     */
    private $ticketType;

    /**
     * @var int
     */
    private $maxTickets;

    /**
     * @return int
     */
    public function getTicketTypeId()
    {
        return $this->ticketTypeId;
    }

    /**
     * @param int $ticketTypeId
     * @return TicketEventType
     */
    public function setTicketTypeId($ticketTypeId)
    {
        $this->propertyChanged('ticketTypeId', $this->ticketTypeId, $ticketTypeId);
        $this->ticketTypeId = $ticketTypeId;
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
     * @return TicketEventType
     */
    public function setEventId($eventId)
    {
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return TicketEventType
     */
    public function setPrice($price)
    {
        $this->propertyChanged('price', $this->price, $price);
        $this->price = $price;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param \DateTime $dateStart
     * @return TicketEventType
     */
    public function setDateStart(\DateTime $dateStart)
    {
        $this->propertyChanged('dateStart', $this->dateStart, $dateStart);
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTime $dateEnd
     * @return TicketEventType
     */
    public function setDateEnd(\DateTime $dateEnd)
    {
        $this->propertyChanged('dateEnd', $this->dateEnd, $dateEnd);
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return TicketEventType
     */
    public function setDescription($description)
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;
        return $this;
    }

    /**
     * @return TicketType
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * @param TicketType $ticketType
     * @return TicketEventType
     */
    public function setTicketType(TicketType $ticketType)
    {
        $this->ticketType = $ticketType;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxTickets()
    {
        return $this->maxTickets;
    }

    /**
     * @param int $maxTickets
     *
     * @return $this
     */
    public function setMaxTickets($maxTickets)
    {
        $this->propertyChanged('maxTickets', $this->maxTickets, $maxTickets);
        $this->maxTickets = $maxTickets;

        return $this;
    }
}
