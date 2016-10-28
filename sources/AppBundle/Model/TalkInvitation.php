<?php


namespace AppBundle\Model;


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
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $talkId;

    /**
     * @var string
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $token;

    /**
     * @var \DateTime
     */
    private $submittedOn;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     *
     * Speaker id who sent the invitation
     *
     */
    private $submittedBy;

    /**
     * @var int
     * @Assert\Choice(choices = {0, 1}, message = "Invalid state")
     */
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
     * @return TalkInvitation
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
    public function getTalkId()
    {
        return $this->talkId;
    }

    /**
     * @param int $talkId
     * @return TalkInvitation
     */
    public function setTalkId($talkId)
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
     * @return TalkInvitation
     */
    public function setEmail($email)
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
     * @return TalkInvitation
     */
    public function setToken($token)
    {
        $this->propertyChanged('token', $this->token, $token);
        $this->token = $token;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSubmittedOn()
    {
        return $this->submittedOn;
    }

    /**
     * @param \DateTime $submittedOn
     * @return TalkInvitation
     */
    public function setSubmittedOn(\DateTime $submittedOn)
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
     * @return TalkInvitation
     */
    public function setState($state)
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
     * @return TalkInvitation
     */
    public function setSubmittedBy($submittedBy)
    {
        $this->propertyChanged('submittedBy', $this->submittedBy, $submittedBy);
        $this->submittedBy = $submittedBy;
        return $this;
    }
}
