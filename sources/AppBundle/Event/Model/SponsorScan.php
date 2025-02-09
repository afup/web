<?php

declare(strict_types=1);


namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class SponsorScan implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $sponsorTicketId;

    /**
     * @var int
     */
    private $ticketId;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \DateTime|null
     */
    private $deletedOn;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSponsorTicketId()
    {
        return $this->sponsorTicketId;
    }

    /**
     * @param string $sponsorTicketId
     * @return $this
     */
    public function setSponsorTicketId($sponsorTicketId): self
    {
        $this->propertyChanged('token', $this->sponsorTicketId, $sponsorTicketId);
        $this->sponsorTicketId = $sponsorTicketId;
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
     * @return $this
     */
    public function setCreatedOn($createdOn): self
    {
        $this->propertyChanged('createdOn', $this->createdOn, $createdOn);
        $this->createdOn = $createdOn;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedOn()
    {
        return $this->deletedOn;
    }

    /**
     * @param \DateTime|null $deletedOn
     * @return $this
     */
    public function setDeletedOn($deletedOn): self
    {
        $this->propertyChanged('deletedOn', $this->deletedOn, $deletedOn);
        $this->deletedOn = $deletedOn;
        return $this;
    }

    /**
     * @return int
     */
    public function getTicketId()
    {
        return $this->ticketId;
    }

    /**
     * @param int $ticketId
     * @return $this
     */
    public function setTicketId($ticketId): self
    {
        $this->propertyChanged('manager', $this->ticketId, $ticketId);
        $this->ticketId = $ticketId;

        return $this;
    }
}
