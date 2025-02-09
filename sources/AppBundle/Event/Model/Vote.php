<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Vote implements NotifyPropertyInterface
{
    use NotifyProperty;
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $sessionId;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $user;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message = "Please set a note !"
     * )
     */
    private $vote = 0;

    private ?\DateTime $submittedOn = null;

    private ?GithubUser $githubUser = null;

    private ?Talk $talk = null;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Vote
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
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param int $sessionId
     * @return Vote
     */
    public function setSessionId($sessionId): self
    {
        $this->propertyChanged('sessionId', $this->sessionId, $sessionId);
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     * @return Vote
     */
    public function setUser($user): self
    {
        $this->propertyChanged('user', $this->user, $user);
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Vote
     */
    public function setComment($comment): self
    {
        $this->propertyChanged('comment', $this->comment, $comment);
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return int
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * @param int $vote
     * @return Vote
     */
    public function setVote($vote): self
    {
        $this->propertyChanged('vote', $this->vote, $vote);
        $this->vote = $vote;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSubmittedOn(): ?\DateTime
    {
        return $this->submittedOn;
    }

    /**
     * @return Vote
     */
    public function setSubmittedOn(\DateTime $submittedOn): self
    {
        $this->propertyChanged('submittedOn', $this->submittedOn, $submittedOn);
        $this->submittedOn = $submittedOn;
        return $this;
    }

    /**
     * @return GithubUser
     */
    public function getGithubUser(): ?GithubUser
    {
        return $this->githubUser;
    }

    /**
     * @return Vote
     */
    public function setGithubUser(GithubUser $githubUser): self
    {
        $this->githubUser = $githubUser;
        return $this;
    }

    /**
     * @return Talk
     */
    public function getTalk(): ?Talk
    {
        return $this->talk;
    }

    /**
     * @return Vote
     */
    public function setTalk(Talk $talk): self
    {
        $this->talk = $talk;
        return $this;
    }
}
