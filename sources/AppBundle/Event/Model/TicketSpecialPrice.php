<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class TicketSpecialPrice implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $eventId;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $token;

    private ?\DateTime $dateStart = null;

    private ?\DateTime $dateEnd = null;

    /**
     * @var string
     */
    private $description;

    private ?TicketType $ticketType = null;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var int
     */
    private $creatorId;

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
     *
     * @return $this
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

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
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

     * @return $this
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

    /**
     * @return $this
     */
    public function setTicketType(TicketType $ticketType): self
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token): self
    {
        $this->propertyChanged('token', $this->token, $token);
        $this->token = $token;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param \DateTime $createdOn
     *
     * @return $this
     */
    public function setCreatedOn($createdOn): self
    {
        $this->propertyChanged('createdOn', $this->createdOn, $createdOn);
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * @param int $creatorId
     *
     * @return $this
     */
    public function setCreatorId($creatorId): self
    {
        $this->propertyChanged('creatorId', $this->creatorId, $creatorId);
        $this->creatorId = $creatorId;

        return $this;
    }
}
