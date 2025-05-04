<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TalkInvitation implements NotifyPropertyInterface
{
    use NotifyProperty;

    const STATE_PENDING = 0;
    const STATE_ACCEPTED = 1;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private $talkId;

    /**
     * @var string
     */
    #[Assert\Email]
    #[Assert\NotBlank]
    private $email;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $token;

    private ?\DateTime $submittedOn = null;

    /**
     * @var int
     *
     */
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)] // Speaker id who sent the invitation
    private $submittedBy;

    /**
     * @var int
     */
    #[Assert\Choice(choices: [0, 1], message: 'Invalid state')]
    private $state;

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
     * @return int
     */
    public function getTalkId()
    {
        return $this->talkId;
    }

    /**
     * @param int $talkId
     */
    public function setTalkId($talkId): self
    {
        $this->propertyChanged('talkId', $this->talkId, $talkId);
        $this->talkId = $talkId;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): self
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;
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
     * @return \DateTime
     */
    public function getSubmittedOn(): ?\DateTime
    {
        return $this->submittedOn;
    }

    public function setSubmittedOn(\DateTime $submittedOn): self
    {
        $this->propertyChanged('submittedOn', $this->submittedOn, $submittedOn);

        $this->submittedOn = $submittedOn;
        return $this;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state): self
    {
        $this->propertyChanged('state', $this->state, $state);

        $this->state = $state;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubmittedBy()
    {
        return $this->submittedBy;
    }

    /**
     * @param int $submittedBy
     */
    public function setSubmittedBy($submittedBy): self
    {
        $this->propertyChanged('submittedBy', $this->submittedBy, $submittedBy);
        $this->submittedBy = $submittedBy;
        return $this;
    }
}
