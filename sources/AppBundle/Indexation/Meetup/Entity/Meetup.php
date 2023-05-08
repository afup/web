<?php

namespace AppBundle\Indexation\Meetup\Entity;

class Meetup
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $title;

    /**
     * @var ?string
     */
    private $description;

    /**
     * @var string
     */
    private $location;

    /**
     * @var int
     */
    private $antenneId;

    /**
     * Meetup constructor.
     *
     * @param string $date
     * @param string $title
     * @param ?string $description
     * @param string $location
     * @param ?int $antenneId
     */
    public function __construct($date, $title, $location, $description, $antenneId = null)
    {
        $this->date = $date;
        $this->title = $title;
        $this->location = $location;
        $this->description = $description;
        $this->antenneId = $antenneId;
    }

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getAntenneId()
    {
        return $this->antenneId;
    }

    /**
     * @param int $antenneId
     */
    public function setAntenneId($antenneId)
    {
        $this->antenneId = $antenneId;
    }
}
