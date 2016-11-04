<?php

namespace AppBundle\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Talk implements NotifyPropertyInterface
{
    use NotifyProperty;

    const TYPE_FULL_LONG = 1;
    const TYPE_FULL_SHORT = 3;
    const TYPE_WORKSHOP = 2;

    const SKILL_JUNIOR = 1;
    const SKILL_MEDIOR = 2;
    const SKILL_SENIOR = 3;
    const SKILL_NA = 0;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $forumId;

    /**
     * @var \DateTime
     */
    private $submittedOn;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $abstract;

    /**
     * @var bool
     */
    private $scheduled = false;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {1, 2, 3}, message = "Choose a valid type")
     */
    private $type = self::TYPE_FULL_LONG;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {0, 1, 2, 3}, message = "Choose a valid skill requirement")
     */
    private $skill = self::SKILL_NA;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Talk
     */
    public function setId($id)
    {
        $id = (int)$id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getForumId()
    {
        return $this->forumId;
    }

    /**
     * @param int $forumId
     * @return Talk
     */
    public function setForumId($forumId)
    {
        $forumId = (int)$forumId;
        $this->propertyChanged('forumId', $this->forumId, $forumId);
        $this->forumId = $forumId;
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
     * @return Talk
     */
    public function setSubmittedOn(\DateTime $submittedOn)
    {
        $this->propertyChanged('submittedOn', $this->submittedOn, $submittedOn);
        $this->submittedOn = $submittedOn;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Talk
     */
    public function setTitle($title)
    {
        $this->propertyChanged('title', $this->title, $title);
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     * @return Talk
     */
    public function setAbstract($abstract)
    {
        $this->propertyChanged('abstract', $this->abstract, $abstract);
        $this->abstract = $abstract;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getScheduled()
    {
        return $this->scheduled;
    }

    /**
     * @param boolean $scheduled
     * @return Talk
     */
    public function setScheduled($scheduled)
    {
        $scheduled = (bool) $scheduled;

        $this->propertyChanged('scheduled', $this->scheduled, $scheduled);
        $this->scheduled = $scheduled;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Talk
     */
    public function setType($type)
    {
        $this->propertyChanged('type', $this->type, $type);
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * @param int $skill
     * @return Talk
     */
    public function setSkill($skill)
    {
        $this->propertyChanged('skill', $this->skill, $skill);
        $this->skill = $skill;
        return $this;
    }
}
