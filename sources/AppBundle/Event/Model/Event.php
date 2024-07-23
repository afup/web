<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

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
     * @var DateTime
     */
    private $dateStart;

    /**
     * @var DateTime
     */
    private $dateEnd;

    /**
     * @var DateTime
     */
    private $dateEndCallForProjects;

    /**
     * @var DateTime
     */
    private $dateEndCallForPapers;

    /**
     * @var DateTime
     */
    private $dateEndVote;

    /**
     * @var DateTime
     */
    private $dateEndPreSales;

    /**
     * @var DateTime
     */
    private $dateEndSales;

    /**
     * @var DateTime
     */
    private $dateEndSalesSponsorToken;

    /**
     * @var DateTime
     */
    private $dateEndSpeakersDinerInfosCollection;

    /**
     * @var DateTime
     */
    private $dateEndHotelInfosCollection;

    /**
     * @var DateTime
     */
    private $datePlanningAnnouncement;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $logoUrl;

    /**
     * @var string
     */
    private $placeName;

    /**
     * @var string
     */
    private $placeAddress;

    private $voteEnabled;

    private $speakersDinerEnabled;

    private $accomodationEnabled;

    private $transportInformationEnabled;

    /**
     * @var string
     */
    private $waitingListUrl;

    /**
     * @var bool
     */
    private $hasPricesDefinedWithVat;

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
     * @return DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param DateTime $dateStart
     * @return Event
     */
    public function setDateStart(DateTime $dateStart)
    {
        $this->propertyChanged('dateStart', $this->dateStart, $dateStart);
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @param DateTime $dateTime
     *
     * @return bool
     */
    public function startsBefore(DateTime $dateTime)
    {
        return $this->getDateStart() < $dateTime;
    }

    /**
     * @return DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param DateTime $dateEnd
     * @return Event
     */
    public function setDateEnd($dateEnd)
    {
        $this->propertyChanged('dateEnd', $this->dateEnd, $dateEnd);
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateEndCallForProjects()
    {
        return $this->dateEndCallForProjects;
    }

    /**
     * @param DateTime $dateEndCallForProjects
     * @return Event
     */
    public function setDateEndCallForProjects($dateEndCallForProjects)
    {
        $this->propertyChanged('dateEndCallForProjects', $this->dateEndCallForProjects, $dateEndCallForProjects);
        $this->dateEndCallForProjects = $dateEndCallForProjects;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateEndCallForPapers()
    {
        return $this->dateEndCallForPapers;
    }

    /**
     * @param DateTime $dateEndCallForPapers
     * @return Event
     */
    public function setDateEndCallForPapers($dateEndCallForPapers)
    {
        $this->propertyChanged('dateEndCallForPapers', $this->dateEndCallForPapers, $dateEndCallForPapers);
        $this->dateEndCallForPapers = $dateEndCallForPapers;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateEndVote()
    {
        if (null === $this->dateEndVote) {
            return $this->getDateEndCallForPapers();
        }

        return $this->dateEndVote;
    }

    /**
     * @param DateTime $dateEndVote
     * @return Event
     */
    public function setDateEndVote(DateTime $dateEndVote = null)
    {
        $this->propertyChanged('dateEndVote', $this->dateEndVote, $dateEndVote);
        $this->dateEndVote = $dateEndVote;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCfpOpen(DateTime $currentDate = null)
    {
        if ($currentDate === null) {
            $currentDate = new DateTime();
        }

        return $this->getDateEndCallForPapers() >= $currentDate;
    }

    /**
     * @param DateTime $currentDate
     *
     * @return bool
     */
    public function isVoteAvailable(DateTime $currentDate = null)
    {
        if ($currentDate === null) {
            $currentDate = new DateTime();
        }

        return $this->getVoteEnabled() && $this->getDateEndVote() >= $currentDate;
    }

    /**
     * @return DateTime
     */
    public function getDateEndPreSales()
    {
        return $this->dateEndPreSales;
    }

    /**
     * @param DateTime $dateEndPreSales
     * @return Event
     */
    public function setDateEndPreSales($dateEndPreSales)
    {
        $this->propertyChanged('dateEndPreSales', $this->dateEndPreSales, $dateEndPreSales);
        $this->dateEndPreSales = $dateEndPreSales;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateEndSales()
    {
        return $this->dateEndSales;
    }

    /**
     * @param DateTime $dateEndSales
     * @return Event
     */
    public function setDateEndSales($dateEndSales)
    {
        $this->propertyChanged('dateEndSales', $this->dateEndSales, $dateEndSales);
        $this->dateEndSales = $dateEndSales;
        return $this;
    }


    /**
     * @return DateTime
     */
    public function getDateEndSalesSponsorToken()
    {
        return $this->dateEndSalesSponsorToken;
    }

    /**
     * @param DateTime $dateEndSalesSponsorToken
     *
     * @return $this
     */
    public function setDateEndSalesSponsorToken($dateEndSalesSponsorToken)
    {
        $this->propertyChanged('dateEndSalesSponsorToken', $this->dateEndSalesSponsorToken, $dateEndSalesSponsorToken);
        $this->dateEndSalesSponsorToken = $dateEndSalesSponsorToken;

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
     * @return string[]
     */
    public function getCFP()
    {
        return $this->CFP;
    }

    /**
     * @param string[] $CFP language indexed array of strings
     * @return Event
     */
    public function setCFP($CFP)
    {
        $this->CFP = $CFP;
        return $this;
    }

    /**
     * @param string $locale
     *
     * @return null|string
     */
    public function getSponsorInfos($locale)
    {
        $cfp = $this->getCFP();
        $key = 'sponsor_management_' . $locale;

        if (!isset($cfp[$key])) {
            return null;
        }

        return $cfp[$key];
    }

    public function getMailInscriptionContent()
    {
        $cfp = $this->getCFP();
        $key = 'mail_inscription_content';

        if (!isset($cfp[$key])) {
            return null;
        }

        return $cfp[$key];
    }

    public function getBecomeSponsorText()
    {
        $cfp = $this->getCFP();
        $key = 'become_sponsor_description';

        if (!isset($cfp[$key])) {
            return null;
        }

        return $cfp[$key];
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

    /**
     * @return string
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * @param string $placeName
     * @return Event
     */
    public function setPlaceName($placeName)
    {
        $this->propertyChanged('placeName', $this->placeName, $placeName);
        $this->placeName = $placeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceAddress()
    {
        return $this->placeAddress;
    }

    /**
     * @param string $placeAddress
     * @return Event
     */
    public function setPlaceAddress($placeAddress)
    {
        $this->propertyChanged('placeAddress', $this->placeAddress, $placeAddress);
        $this->placeAddress = $placeAddress;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateEndSpeakersDinerInfosCollection()
    {
        return $this->dateEndSpeakersDinerInfosCollection;
    }

    /**
     * @param DateTime $dateEndSpeakersDinerInfosCollection
     *
     * @return $this
     */
    public function setDateEndSpeakersDinerInfosCollection(DateTime $dateEndSpeakersDinerInfosCollection = null)
    {
        $this->propertyChanged('dateEndSpeakersDinerInfosCollection', $this->dateEndSpeakersDinerInfosCollection, $dateEndSpeakersDinerInfosCollection);
        $this->dateEndSpeakersDinerInfosCollection = $dateEndSpeakersDinerInfosCollection;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateEndHotelInfosCollection()
    {
        return $this->dateEndHotelInfosCollection;
    }

    /**
     * @param DateTime $dateEndHotelInfosCollection
     *
     * @return $this
     */
    public function setDateEndHotelInfosCollection(DateTime $dateEndHotelInfosCollection = null)
    {
        $this->propertyChanged('dateEndHotelInfosCollection', $this->dateEndHotelInfosCollection, $dateEndHotelInfosCollection);
        $this->dateEndHotelInfosCollection = $dateEndHotelInfosCollection;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPlanningDisplayable()
    {
        $date = $this->getDatePlanningAnnouncement();
        return $date === null || new DateTime() >= $date;
    }

    /**
     * @return DateTime
     */
    public function getDatePlanningAnnouncement()
    {
        return $this->datePlanningAnnouncement;
    }

    /**
     * @param DateTime|null $datePlanningAnnouncement
     *
     * @return $this
     */
    public function setDatePlanningAnnouncement(DateTime $datePlanningAnnouncement = null)
    {
        $this->propertyChanged('datePlanningAnnouncement', $this->datePlanningAnnouncement, $datePlanningAnnouncement);
        $this->datePlanningAnnouncement = $datePlanningAnnouncement;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVoteEnabled()
    {
        return $this->voteEnabled;
    }

    /**
     * @param bool $voteEnabled
     *
     * @return $this
     */
    public function setVoteEnabled($voteEnabled)
    {
        $voteEnabled = (bool) $voteEnabled;

        $this->propertyChanged('voteEnabled', $this->voteEnabled, $voteEnabled);
        $this->voteEnabled = $voteEnabled;

        return $this;
    }

    public function lastsOneDay()
    {
        if (null === ($dateStart = $this->getDateStart())) {
            throw new \RuntimeException('Undefined start date for event ' . $this->getId());
        }

        if (null === ($dateEnd = $this->getDateEnd())) {
            throw new \RuntimeException('Undefined end date for event ' . $this->getId());
        }

        return $dateStart->format('Y-m-d') === $dateEnd->format('Y-m-d');
    }

    /**
     * @return bool
     */
    public function getSpeakersDinerEnabled()
    {
        return $this->speakersDinerEnabled;
    }

    /**
     * @param bool $speakersDinerEnabled
     *
     * @return $this
     */
    public function setSpeakersDinerEnabled($speakersDinerEnabled)
    {
        $speakersDinerEnabled = (bool) $speakersDinerEnabled;

        $this->propertyChanged('speakersDinerEnabled', $this->speakersDinerEnabled, $speakersDinerEnabled);
        $this->speakersDinerEnabled = $speakersDinerEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAccomodationEnabled()
    {
        return $this->accomodationEnabled;
    }

    /**
     * @param bool $accomodationEnabled
     *
     * @return $this
     */
    public function setAccomodationEnabled($accomodationEnabled)
    {
        $accomodationEnabled = (bool) $accomodationEnabled;

        $this->propertyChanged('accomodationEnabled', $this->accomodationEnabled, $accomodationEnabled);
        $this->accomodationEnabled = $accomodationEnabled;

        return $this;
    }

    public function isAfupDay()
    {
        return substr($this->getTitle(), 0, 8) == 'AFUP Day';
    }

    /**
     * @return string
     */
    public function getWaitingListUrl()
    {
        return $this->waitingListUrl;
    }

    /**
     * @param string $waitingListUrl
     *
     * @return $this
     */
    public function setWaitingListUrl($waitingListUrl)
    {
        $this->propertyChanged('waitingListUrl', $this->waitingListUrl, $waitingListUrl);
        $this->waitingListUrl = $waitingListUrl;

        return $this;
    }

    public function hasPricesDefinedWithVat(): bool
    {
        return (bool) $this->hasPricesDefinedWithVat;
    }

    /**
     * @param bool $hasPricesDefinedWithVat
     *
     * @return $this
     */
    public function setHasPricesDefinedWithVat(bool $hasPricesDefinedWithVat)
    {
        $this->propertyChanged('hasPricesDefinedWithVat', $this->hasPricesDefinedWithVat, $hasPricesDefinedWithVat);
        $this->hasPricesDefinedWithVat = $hasPricesDefinedWithVat;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTransportInformationEnabled()
    {
        return $this->transportInformationEnabled;
    }

    /**
     * @param bool $transportInformationEnabled
     *
     * @return $this
     */
    public function setTransportInformationEnabled($transportInformationEnabled)
    {
        $transportInformationEnabled = (bool) $transportInformationEnabled;

        $this->propertyChanged('transportInformationEnabled', $this->transportInformationEnabled, $transportInformationEnabled);
        $this->transportInformationEnabled = $transportInformationEnabled;

        return $this;
    }

    public function isOnline()
    {
        return false !== strpos($this->getPath(), 'enligne');
    }

    public static function getInscriptionAttachmentDir()
    {
        return __DIR__ . '/../../../../htdocs/uploads/mail_inscription_attachment/';
    }

    public static function getInscriptionAttachmentFilepath($eventPath)
    {
        return self::getInscriptionAttachmentDir() . $eventPath . '.pdf';
    }

    public static function hasInscriptionAttachment($eventPath)
    {
        return is_file(self::getInscriptionAttachmentFilepath($eventPath));
    }

    public static function getSponsorFileDir()
    {
        return __DIR__ . '/../../../../htdocs/docs/';
    }

    public static function getSponsorFilePublicPath($eventPath, $language)
    {
        return '/docs/' . $eventPath . '-sponsoring-' . $language . '.pdf';
    }

    public static function getSponsorFilePath($eventPath, $language)
    {
        return self::getSponsorFileDir() . $eventPath . '-sponsoring-' . $language . '.pdf';
    }

    public static function hasSponsorFile($eventPath, $language)
    {
        return is_file(self::getSponsorFilePath($eventPath, $language));
    }
}
