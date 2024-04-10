<?php


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
     * @Assert\NotBlank()
     */
    private $company;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=30, max=64)
     */
    private $token;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $contactEmail;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=1, max=20)
     */
    private $maxInvitations = 0;

    /**
     * @var int
     */
    private $usedInvitations = 0;

    /**
     * @var int
     */
    private $idForum;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \DateTime
     */
    private $editedOn;

    /**
     * @var int
     */
    private $creatorId;

    /**
     * @var bool
     */
    private $qrCodesScanner = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SponsorTicket
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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @return SponsorTicket
     */
    public function setCompany($company)
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
     * @return SponsorTicket
     */
    public function setToken($token)
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
     * @return SponsorTicket
     */
    public function setContactEmail($contactEmail)
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
     * @return SponsorTicket
     */
    public function setMaxInvitations($maxInvitations)
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
     * @return SponsorTicket
     */
    public function setUsedInvitations($usedInvitations)
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
     * @return SponsorTicket
     */
    public function setIdForum($idForum)
    {
        $this->propertyChanged('idForum', $this->idForum, $idForum);
        $this->idForum = $idForum;
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
     * @return SponsorTicket
     */
    public function setCreatedOn(\DateTime $createdOn)
    {
        $this->propertyChanged('createdOn', $this->createdOn, $createdOn);
        $this->createdOn = $createdOn;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * @param \DateTime $editedOn
     * @return SponsorTicket
     */
    public function setEditedOn(\DateTime $editedOn)
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
     * @return SponsorTicket
     */
    public function setCreatorId($creatorId)
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
    public function getQrCodesScanner()
    {
        return $this->qrCodesScanner;
    }

    /**
     * @param bool $qrCodesScanner
     * @return SponsorTicket
     */
    public function setQrCodesScanner($qrCodesScanner)
    {
        $this->propertyChanged('qrCodesScanner', $this->qrCodesScanner, $qrCodesScanner);
        $this->qrCodesScanner = $qrCodesScanner;
        return $this;
    }
}
