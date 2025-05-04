<?php

declare(strict_types=1);


namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SponsorTicket implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $company;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Length(min: 30, max: 64)]
    private $token;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    private $contactEmail;

    /**
     * @var int
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 20)]
    private $maxInvitations = 0;

    /**
     * @var int
     */
    private $usedInvitations = 0;

    /**
     * @var int
     */
    private $idForum;

    private ?\DateTime $createdOn = null;

    private ?\DateTime $editedOn = null;

    /**
     * @var int
     */
    private $creatorId;

    /**
     * @var bool
     */
    private $qrCodesScannerAvailable = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company): self
    {
        $this->propertyChanged('company', $this->company, $company);
        $this->company = $company;
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
     */
    public function setToken($token): self
    {
        $this->propertyChanged('token', $this->token, $token);
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail): self
    {
        $this->propertyChanged('contactEmail', $this->contactEmail, $contactEmail);
        $this->contactEmail = $contactEmail;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxInvitations()
    {
        return $this->maxInvitations;
    }

    /**
     * @param int $maxInvitations
     */
    public function setMaxInvitations($maxInvitations): self
    {
        $this->propertyChanged('maxInvitations', $this->maxInvitations, $maxInvitations);
        $this->maxInvitations = $maxInvitations;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsedInvitations()
    {
        return $this->usedInvitations;
    }

    /**
     * @param int $usedInvitations
     */
    public function setUsedInvitations($usedInvitations): self
    {
        $this->propertyChanged('usedInvitations', $this->usedInvitations, $usedInvitations);
        $this->usedInvitations = $usedInvitations;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdForum()
    {
        return $this->idForum;
    }

    /**
     * @param int $idForum
     */
    public function setIdForum($idForum): self
    {
        $this->propertyChanged('idForum', $this->idForum, $idForum);
        $this->idForum = $idForum;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn(): ?\DateTime
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTime $createdOn): self
    {
        $this->propertyChanged('createdOn', $this->createdOn, $createdOn);
        $this->createdOn = $createdOn;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEditedOn(): ?\DateTime
    {
        return $this->editedOn;
    }

    public function setEditedOn(\DateTime $editedOn): self
    {
        $this->propertyChanged('editedOn', $this->editedOn, $editedOn);
        $this->editedOn = $editedOn;
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
     */
    public function setCreatorId($creatorId): self
    {
        $this->propertyChanged('creatorId', $this->creatorId, $creatorId);
        $this->creatorId = $creatorId;
        return $this;
    }

    /**
     * @return int
     */
    public function getPendingInvitations()
    {
        return $this->maxInvitations - $this->usedInvitations;
    }

    /**
     * @return bool
     */
    public function getQrCodesScannerAvailable()
    {
        return $this->qrCodesScannerAvailable;
    }

    /**
     * @param bool $qrCodesScannerAvailable
     */
    public function setQrCodesScannerAvailable($qrCodesScannerAvailable): self
    {
        $this->propertyChanged('qrCodesScannerAvailable', $this->qrCodesScannerAvailable, $qrCodesScannerAvailable);
        $this->qrCodesScannerAvailable = $qrCodesScannerAvailable;
        return $this;
    }
}
