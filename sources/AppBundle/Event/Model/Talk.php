<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Cocur\Slugify\Slugify;
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
     * @var bool
     */
    private $needsMentoring = false;

    /**
     * @var string|null
     */
    private $youTubeId;

    /**
     * @var string|null
     */
    private $slidesUrl;

    /**
     * @var string|null
     */
    private $blogPostUrl;

    /**
     * @var string|null
     */
    private $joindinId;

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

    public function getSkillTranslationKey()
    {
        return 'skill.' . $this->skill;
    }

    public function getTypeTranslationKey()
    {
        return 'type.' . $this->type;
    }

    /**
     * @return bool
     */
    public function getNeedsMentoring()
    {
        return $this->needsMentoring;
    }

    /**
     * @param bool $needsMentoring
     *
     * @return $this
     */
    public function setNeedsMentoring($needsMentoring)
    {
        $this->propertyChanged('needsMentoring', $this->needsMentoring, $needsMentoring);
        $this->needsMentoring = $needsMentoring;

        return $this;
    }

    /**
     * @return int
     */
    public function getYoutubeId()
    {
        if (0 === strlen($this->youTubeId)) {
            return null;
        }

        return $this->youTubeId;
    }

    /**
     * @param int $youtubeId
     *
     * @return Talk
     */
    public function setYoutubeId($youtubeId)
    {
        $this->propertyChanged('youtubeId', $this->youTubeId, $youtubeId);
        $this->youTubeId = $youtubeId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function hasYoutubeId()
    {
        return null !== $this->getYoutubeId();
    }

    /**
     * @return null|string
     */
    public function getYoutubeUrl()
    {
        if (!$this->hasYoutubeId()) {
            return null;
        }

        return 'https://www.youtube.com/watch?v=' . $this->getYoutubeId();
    }

    /**
     * @return int
     */
    public function getSlidesUrl()
    {
        if (0 === strlen($this->slidesUrl)) {
            return null;
        }

        return $this->slidesUrl;
    }

    /**
     * @param int $slidesUrl
     *
     * @return Talk
     */
    public function setSlidesUrl($slidesUrl)
    {
        $this->propertyChanged('slidesUrl', $this->slidesUrl, $slidesUrl);
        $this->slidesUrl = $slidesUrl;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSlidesUrl()
    {
        return null !== $this->getSlidesUrl();
    }

    /**
     * @return int
     */
    public function getJoindinId()
    {
        return $this->joindinId;
    }

    /**
     * @param int $joindInId
     *
     * @return Talk
     */
    public function setJoindinId($joindInId)
    {
        $this->propertyChanged('joindinId', $this->joindinId, $joindInId);
        $this->joindinId = $joindInId;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasJoindinId()
    {
        return null !== $this->joindinId;
    }

    /**
     * @return null|string
     */
    public function getJoindinUrl()
    {
        if (!$this->hasJoindinId()) {
            return null;
        }

        return 'https://legacy.joind.in/talk/view/' . $this->getJoindinId();
    }

    /**
     * @return int
     */
    public function getBlogPostUrl()
    {
        if (0 === strlen($this->blogPostUrl)) {
            return null;
        }

        return $this->blogPostUrl;
    }

    /**
     * @param int $blogPostUrl
     *
     * @return Talk
     */
    public function setBlogPostUrl($blogPostUrl)
    {
        $this->propertyChanged('blogPostUrl', $this->blogPostUrl, $blogPostUrl);
        $this->blogPostUrl = $blogPostUrl;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasBlogPostUrl()
    {
        return null !== $this->getBlogPostUrl();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        $slugify = new Slugify();
        return $slugify->slugify($this->getTitle());
    }
}
