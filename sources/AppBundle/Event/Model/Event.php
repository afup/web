<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Event implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $seats;

    /**
     * @var string[] language indexed array of strings
     */
    private $CFP;

    /**
     * @var \DateTime
     */
    private $dateStart;

    /**
     * @var \DateTime
     */
    private $dateEnd;

    /**
     * @var \DateTime
     */
    private $dateEndCallForProjects;

    /**
     * @var \DateTime
     */
    private $dateEndCallForPapers;

    /**
     * @var \DateTime
     */
    private $dateEndVote;

    /**
     * @var \DateTime
     */
    private $dateEndPreSales;

    /**
     * @var \DateTime
     */
    private $dateEndSales;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $trelloListId;

    /**
     * @var string
     */
    private $logoUrl;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Event
     */
    public function setId($id)
    {
        $id = (int) $id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
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
     * @return Event
     */
    public function setTitle($title)
    {
        $title = (string) $title;
        $this->propertyChanged('title', $this->title, $title);
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getSeats()
    {
        return $this->seats;
    }

    /**
     * @param int $seats
     * @return Event
     */
    public function setSeats($seats)
    {
        $seats = (int) $seats;
        $this->propertyChanged('seats', $this->seats, $seats);
        $this->seats = $seats;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param \DateTime $dateStart
     * @return Event
     */
    public function setDateStart(\DateTime $dateStart)
    {
        $this->propertyChanged('dateStart', $this->dateStart, $dateStart);
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    public function startsBefore(\DateTime $dateTime)
    {
        return $this->getDateStart() < $dateTime;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTime $dateEnd
     * @return Event
     */
    public function setDateEnd($dateEnd)
    {
        $this->propertyChanged('dateEnd', $this->dateEnd, $dateEnd);
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEndCallForProjects()
    {
        return $this->dateEndCallForProjects;
    }

    /**
     * @param \DateTime $dateEndCallForProjects
     * @return Event
     */
    public function setDateEndCallForProjects($dateEndCallForProjects)
    {
        $this->propertyChanged('dateEndCallForProjects', $this->dateEndCallForProjects, $dateEndCallForProjects);
        $this->dateEndCallForProjects = $dateEndCallForProjects;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEndCallForPapers()
    {
        return $this->dateEndCallForPapers;
    }

    /**
     * @param \DateTime $dateEndCallForPapers
     * @return Event
     */
    public function setDateEndCallForPapers($dateEndCallForPapers)
    {
        $this->propertyChanged('dateEndCallForPapers', $this->dateEndCallForPapers, $dateEndCallForPapers);
        $this->dateEndCallForPapers = $dateEndCallForPapers;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEndVote()
    {
        return $this->dateEndVote;
    }

    /**
     * @param \DateTime $dateEndVote
     * @return Event
     */
    public function setDateEndVote(\DateTime $dateEndVote)
    {
        $this->propertyChanged('dateEndVote', $this->dateEndVote, $dateEndVote);
        $this->dateEndVote = $dateEndVote;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEndPreSales()
    {
        return $this->dateEndPreSales;
    }

    /**
     * @param \DateTime $dateEndPreSales
     * @return Event
     */
    public function setDateEndPreSales($dateEndPreSales)
    {
        $this->propertyChanged('dateEndPreSales', $this->dateEndPreSales, $dateEndPreSales);
        $this->dateEndPreSales = $dateEndPreSales;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEndSales()
    {
        return $this->dateEndSales;
    }

    /**
     * @param \DateTime $dateEndSales
     * @return Event
     */
    public function setDateEndSales($dateEndSales)
    {
        $this->propertyChanged('dateEndSales', $this->dateEndSales, $dateEndSales);
        $this->dateEndSales = $dateEndSales;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Event
     */
    public function setPath($path)
    {
        $this->propertyChanged('path', $this->path, $path);
        $this->path = $path;
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getCFP()
    {
        return $this->CFP;
    }

    /**
     * @param \string[] $CFP language indexed array of strings
     * @return Event
     */
    public function setCFP($CFP)
    {
        $this->CFP = $CFP;
        return $this;
    }

    public function getMailTemplate()
    {
        return 'confirmation-inscription-' . $this->getPath();
    }

    /**
     * @return string
     */
    public function getTrelloListId()
    {
        return $this->trelloListId;
    }

    /**
     * @param string $trelloListId
     * @return Event
     */
    public function setTrelloListId($trelloListId)
    {
        $this->propertyChanged('trelloListId', $this->trelloListId, $trelloListId);
        $this->trelloListId = $trelloListId;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * @param string $logoUrl
     *
     * @return Event
     */
    public function setLogoUrl($logoUrl)
    {
        $this->propertyChanged('logoUrl', $this->logoUrl, $logoUrl);
        $this->logoUrl = $logoUrl;

        return $this;
    }
}
