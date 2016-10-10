<?php


namespace AppBundle\Model;


use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Talk implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $forumId;

    /**
     * @var \DateTime
     */
    private $submittedOn;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $abstract;

    /**
     * @var bool
     */
    private $scheduled = false;

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
    public function isScheduled()
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
}
