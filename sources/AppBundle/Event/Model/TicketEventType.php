<?php

declare(strict_types=1);

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

    private ?\DateTime $dateStart = null;

    private ?\DateTime $dateEnd = null;

    /**
     * @var string
     */
    private $description;

    private ?TicketType $ticketType = null;

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
     */
    public function setTicketTypeId($ticketTypeId): self
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
     */
    public function setEventId($eventId): self
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
     */
    public function setPrice($price): self
    {
        $this->propertyChanged('price', $this->price, $price);
        $this->price = $price;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateStart(): ?\DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTime $dateStart): self
    {
        $this->propertyChanged('dateStart', $this->dateStart, $dateStart);
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd(): ?\DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTime $dateEnd): self
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
     */
    public function setDescription($description): self
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;
        return $this;
    }

    /**
     * @return TicketType
     */
    public function getTicketType(): ?TicketType
    {
        return $this->ticketType;
    }

    public function setTicketType(TicketType $ticketType): self
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
    public function setMaxTickets($maxTickets): self
    {
        $this->propertyChanged('maxTickets', $this->maxTickets, $maxTickets);
        $this->maxTickets = $maxTickets;

        return $this;
    }
}
