<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use DateTime;

class Event implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;

    private ?string $title = null;

    private ?int $seats = null;

    /**
     * @var string[] language indexed array of strings
     */
    private $CFP;

    private ?\DateTime $dateStart = null;

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

    private ?\DateTime $dateEndVote = null;

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

    private ?\DateTime $dateEndSpeakersDinerInfosCollection = null;

    private ?\DateTime $dateEndHotelInfosCollection = null;

    private ?\DateTime $datePlanningAnnouncement = null;

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

    private ?bool $voteEnabled = null;

    private ?bool $speakersDinerEnabled = null;

    private ?bool $accomodationEnabled = null;

    private ?bool $transportInformationEnabled = null;

    /**
     * @var string
     */
    private $waitingListUrl;

    private bool $hasPricesDefinedWithVat = true;

    private ?DateTime $archivedAt = null;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Event
     */
    public function setId($id): self
    {
        $id = (int) $id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Event
     */
    public function setTitle($title): self
    {
        $title = (string) $title;
        $this->propertyChanged('title', $this->title, $title);
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getSeats(): ?int
    {
        return $this->seats;
    }

    /**
     * @param int $seats
     * @return Event
     */
    public function setSeats($seats): self
    {
        $seats = (int) $seats;
        $this->propertyChanged('seats', $this->seats, $seats);
        $this->seats = $seats;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateStart(): ?\DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(?DateTime $dateStart = null): self
    {
        $this->propertyChanged('dateStart', $this->dateStart, $dateStart);
        $this->dateStart = $dateStart;
        return $this;
    }

    public function startsBefore(DateTime $dateTime): bool
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
    public function setDateEnd($dateEnd): self
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
    public function setDateEndCallForProjects($dateEndCallForProjects): self
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
    public function setDateEndCallForPapers($dateEndCallForPapers): self
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
        if (!$this->dateEndVote instanceof \DateTime) {
            return $this->getDateEndCallForPapers();
        }

        return $this->dateEndVote;
    }

    /**
     * @return Event
     */
    public function setDateEndVote(DateTime $dateEndVote = null): self
    {
        $this->propertyChanged('dateEndVote', $this->dateEndVote, $dateEndVote);
        $this->dateEndVote = $dateEndVote;
        return $this;
    }

    public function isCfpOpen(DateTime $currentDate = null): bool
    {
        if (!$currentDate instanceof \DateTime) {
            $currentDate = new DateTime();
        }

        return $this->getDateEndCallForPapers() >= $currentDate;
    }

    public function isVoteAvailable(DateTime $currentDate = null): bool
    {
        if (!$currentDate instanceof \DateTime) {
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
    public function setDateEndPreSales($dateEndPreSales): self
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
    public function setDateEndSales($dateEndSales): self
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
    public function setDateEndSalesSponsorToken($dateEndSalesSponsorToken): self
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
    public function setPath($path): self
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
    public function setCFP($CFP): self
    {
        $this->propertyChanged('CFP', json_encode($this->CFP), json_encode($CFP));
        $this->CFP = $CFP;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSponsorInfos(string $locale)
    {
        $cfp = $this->getCFP();
        $key = 'sponsor_management_' . $locale;

        if (!isset($cfp[$key])) {
            return null;
        }

        return $cfp[$key];
    }

    public function getMailInscriptionContent(): ?string
    {
        $cfp = $this->getCFP();
        $key = 'mail_inscription_content';

        if (!isset($cfp[$key])) {
            return null;
        }

        return $cfp[$key];
    }

    public function getBecomeSponsorText(): ?string
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
    public function setLogoUrl($logoUrl): self
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
    public function setPlaceName($placeName): self
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
    public function setPlaceAddress($placeAddress): self
    {
        $this->propertyChanged('placeAddress', $this->placeAddress, $placeAddress);
        $this->placeAddress = $placeAddress;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateEndSpeakersDinerInfosCollection(): ?\DateTime
    {
        return $this->dateEndSpeakersDinerInfosCollection;
    }

    /**
     * @return $this
     */
    public function setDateEndSpeakersDinerInfosCollection(DateTime $dateEndSpeakersDinerInfosCollection = null): self
    {
        $this->propertyChanged('dateEndSpeakersDinerInfosCollection', $this->dateEndSpeakersDinerInfosCollection, $dateEndSpeakersDinerInfosCollection);
        $this->dateEndSpeakersDinerInfosCollection = $dateEndSpeakersDinerInfosCollection;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateEndHotelInfosCollection(): ?\DateTime
    {
        return $this->dateEndHotelInfosCollection;
    }

    /**
     * @return $this
     */
    public function setDateEndHotelInfosCollection(DateTime $dateEndHotelInfosCollection = null): self
    {
        $this->propertyChanged('dateEndHotelInfosCollection', $this->dateEndHotelInfosCollection, $dateEndHotelInfosCollection);
        $this->dateEndHotelInfosCollection = $dateEndHotelInfosCollection;

        return $this;
    }

    public function isPlanningDisplayable(): bool
    {
        $date = $this->getDatePlanningAnnouncement();
        return !$date instanceof \DateTime || new DateTime() >= $date;
    }

    /**
     * @return DateTime
     */
    public function getDatePlanningAnnouncement(): ?\DateTime
    {
        return $this->datePlanningAnnouncement;
    }

    /**
     * @param DateTime|null $datePlanningAnnouncement
     *
     * @return $this
     */
    public function setDatePlanningAnnouncement(DateTime $datePlanningAnnouncement = null): self
    {
        $this->propertyChanged('datePlanningAnnouncement', $this->datePlanningAnnouncement, $datePlanningAnnouncement);
        $this->datePlanningAnnouncement = $datePlanningAnnouncement;

        return $this;
    }

    public function getVoteEnabled(): ?bool
    {
        return $this->voteEnabled;
    }

    /**
     * @param bool $voteEnabled
     *
     * @return $this
     */
    public function setVoteEnabled($voteEnabled): self
    {
        $voteEnabled = (bool) $voteEnabled;

        $this->propertyChanged('voteEnabled', $this->voteEnabled, $voteEnabled);
        $this->voteEnabled = $voteEnabled;

        return $this;
    }

    public function lastsOneDay(): bool
    {
        if (!($dateStart = $this->getDateStart()) instanceof \DateTime) {
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
    public function getSpeakersDinerEnabled(): ?bool
    {
        return $this->speakersDinerEnabled;
    }

    /**
     * @param bool $speakersDinerEnabled
     *
     * @return $this
     */
    public function setSpeakersDinerEnabled($speakersDinerEnabled): self
    {
        $speakersDinerEnabled = (bool) $speakersDinerEnabled;

        $this->propertyChanged('speakersDinerEnabled', $this->speakersDinerEnabled, $speakersDinerEnabled);
        $this->speakersDinerEnabled = $speakersDinerEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAccomodationEnabled(): ?bool
    {
        return $this->accomodationEnabled;
    }

    /**
     * @param bool $accomodationEnabled
     *
     * @return $this
     */
    public function setAccomodationEnabled($accomodationEnabled): self
    {
        $accomodationEnabled = (bool) $accomodationEnabled;

        $this->propertyChanged('accomodationEnabled', $this->accomodationEnabled, $accomodationEnabled);
        $this->accomodationEnabled = $accomodationEnabled;

        return $this;
    }

    public function isAfupDay(): bool
    {
        return str_starts_with($this->getTitle(), 'AFUP Day');
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
    public function setWaitingListUrl($waitingListUrl): self
    {
        $this->propertyChanged('waitingListUrl', $this->waitingListUrl, $waitingListUrl);
        $this->waitingListUrl = $waitingListUrl;

        return $this;
    }

    public function hasPricesDefinedWithVat(): bool
    {
        return $this->hasPricesDefinedWithVat;
    }

    public function gethasPricesDefinedWithVat(): bool
    {
        return $this->hasPricesDefinedWithVat;
    }

    /**
     * @return $this
     */
    public function setHasPricesDefinedWithVat(bool $hasPricesDefinedWithVat): self
    {
        $this->propertyChanged('hasPricesDefinedWithVat', $this->hasPricesDefinedWithVat, $hasPricesDefinedWithVat);
        $this->hasPricesDefinedWithVat = $hasPricesDefinedWithVat;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTransportInformationEnabled(): ?bool
    {
        return $this->transportInformationEnabled;
    }

    /**
     * @param bool $transportInformationEnabled
     *
     * @return $this
     */
    public function setTransportInformationEnabled($transportInformationEnabled): self
    {
        $transportInformationEnabled = (bool) $transportInformationEnabled;

        $this->propertyChanged('transportInformationEnabled', $this->transportInformationEnabled, $transportInformationEnabled);
        $this->transportInformationEnabled = $transportInformationEnabled;

        return $this;
    }

    public function getArchivedAt(): ?DateTime
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?DateTime $archivedAt): self
    {
        $this->propertyChanged('archivedAt', $this->archivedAt, $archivedAt);
        $this->archivedAt = $archivedAt;

        return $this;
    }

    public function isOnline(): bool
    {
        return str_contains($this->getPath(), 'enligne');
    }

    public static function getInscriptionAttachmentDir(): string
    {
        return __DIR__ . '/../../../../htdocs/uploads/mail_inscription_attachment/';
    }

    public static function getInscriptionAttachmentFilepath(?string $eventPath): string
    {
        return self::getInscriptionAttachmentDir() . $eventPath . '.pdf';
    }

    public static function hasInscriptionAttachment(?string $eventPath): bool
    {
        return is_file(self::getInscriptionAttachmentFilepath($eventPath));
    }

    public static function getInscriptionAttachmentPublicPath(string $eventPath): string
    {
        return '/uploads/mail_inscription_attachment/' . $eventPath . '.pdf';
    }


    public static function getSponsorFileDir(): string
    {
        return __DIR__ . '/../../../../htdocs/docs/';
    }

    public static function getSponsorFilePublicPath(string $eventPath, string $language): string
    {
        return '/docs/' . $eventPath . '-sponsoring-' . $language . '.pdf';
    }

    public static function getSponsorFilePath(?string $eventPath, string $language): string
    {
        return self::getSponsorFileDir() . $eventPath . '-sponsoring-' . $language . '.pdf';
    }

    public static function hasSponsorFile(?string $eventPath, string $language): bool
    {
        return is_file(self::getSponsorFilePath($eventPath, $language));
    }
}
