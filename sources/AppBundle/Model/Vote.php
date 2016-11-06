<?php

namespace AppBundle\Model;

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

    /**
     * @var \DateTime
     */
    private $submittedOn;

    /**
     * @var GithubUser
     */
    private $githubUser;

    /**
     * @var Talk
     */
    private $talk;

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
    public function setId($id)
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
    public function setSessionId($sessionId)
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
    public function setUser($user)
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
    public function setComment($comment)
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
    public function setVote($vote)
    {
        $this->propertyChanged('vote', $this->vote, $vote);
        $this->vote = $vote;
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
     * @return Vote
     */
    public function setSubmittedOn(\DateTime $submittedOn)
    {
        $this->propertyChanged('submittedOn', $this->submittedOn, $submittedOn);
        $this->submittedOn = $submittedOn;
        return $this;
    }

    /**
     * @return GithubUser
     */
    public function getGithubUser()
    {
        return $this->githubUser;
    }

    /**
     * @param GithubUser $githubUser
     * @return Vote
     */
    public function setGithubUser(GithubUser $githubUser)
    {
        $this->githubUser = $githubUser;
        return $this;
    }

    /**
     * @return Talk
     */
    public function getTalk()
    {
        return $this->talk;
    }

    /**
     * @param Talk $talk
     * @return Vote
     */
    public function setTalk(Talk $talk)
    {
        $this->talk = $talk;
        return $this;
    }
}
