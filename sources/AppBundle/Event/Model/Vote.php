<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Vote implements NotifyPropertyInterface
{
    use NotifyProperty;

    private int $id;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private int $sessionId = 0;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private int $user = 0;

    private ?string $comment = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(value: 0, message: 'Please set a note !')]
    private int $vote = 0;

    private ?\DateTime $submittedOn = null;

    private ?GithubUser $githubUser = null;

    private ?Talk $talk = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    public function getSessionId(): int
    {
        return $this->sessionId;
    }

    public function setSessionId(int $sessionId): self
    {
        $this->propertyChanged('sessionId', $this->sessionId, $sessionId);
        $this->sessionId = $sessionId;
        return $this;
    }

    public function getUser(): int
    {
        return $this->user;
    }

    public function setUser(int $user): self
    {
        $this->propertyChanged('user', $this->user, $user);
        $this->user = $user;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->propertyChanged('comment', $this->comment, $comment);
        $this->comment = $comment;
        return $this;
    }

    public function getVote(): int
    {
        return $this->vote;
    }

    public function setVote(int $vote): self
    {
        $this->propertyChanged('vote', $this->vote, $vote);
        $this->vote = $vote;
        return $this;
    }

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

    public function getGithubUser(): ?GithubUser
    {
        return $this->githubUser;
    }

    public function setGithubUser(GithubUser $githubUser): self
    {
        $this->githubUser = $githubUser;
        return $this;
    }

    public function getTalk(): ?Talk
    {
        return $this->talk;
    }

    public function setTalk(Talk $talk): self
    {
        $this->talk = $talk;
        return $this;
    }
}
