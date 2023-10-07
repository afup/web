<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

class Meetup implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $location = null;

    /**
     * @var ?string
     */
    private $description;

    /**
     * @var string
     */
    private $antenneName;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Meetup
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return Meetup
     */
    public function setDate($date)
    {
        $this->propertyChanged('date', $this->date, $date);
        $this->date = $date;

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
     *
     * @return Meetup
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     *
     * @return Meetup
     */
    public function setLocation($location = null)
    {
        $this->propertyChanged('location', $this->location, $location);
        $this->location = $location;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param ?string $description
     *
     * @return Meetup
     */
    public function setDescription($description)
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getAntenneName()
    {
        return $this->antenneName;
    }

    /**
     * @param string $antenneName
     *
     * @return Meetup
     */
    public function setAntenneName($antenneName)
    {
        $this->propertyChanged('antenneName', $this->antenneName, $antenneName);
        $this->antenneName = $antenneName;

        return $this;
    }
}
