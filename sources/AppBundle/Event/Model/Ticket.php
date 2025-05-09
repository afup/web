<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Validator\Constraints as Assert;
use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

/**
 * @Assert\LoggedInMember(groups={"personal"})
 * @Assert\PublicTicket(groups={"not_logged_in"})
 * @Assert\AvailableTicket()
 */
class Ticket implements NotifyPropertyInterface
{
    use NotifyProperty;

    const TRANSPORT_MODES = [
        AFUP_TRANSPORT_MODE_SEUL_THERMIQUE => 'Voiture seul·e',
        AFUP_TRANSPORT_MODE_SEUL_ELECTRIQUE => 'Voiture seul·e électrique',
        AFUP_TRANSPORT_MODE_SEUL_HYBRIDE => 'Voiture seul·e hybride',
        AFUP_TRANSPORT_MODE_PASSAGERS_THERMIQUE=> 'Voiture avec passagers·ères',
        AFUP_TRANSPORT_MODE_PASSAGERS_ELECTRIQUE => 'Voiture avec passagers·ères électrique',
        AFUP_TRANSPORT_MODE_PASSAGERS_HYBRIDE => 'Voiture avec passagers·ères hybride',
        AFUP_TRANSPORT_MODE_BUS => 'Bus',
        AFUP_TRANSPORT_MODE_TRAIN => 'Train',
        AFUP_TRANSPORT_MODE_AVION_ECO => 'Avion classe économique',
        AFUP_TRANSPORT_MODE_AVION_BUSINESS => 'Avion classe business',
        AFUP_TRANSPORT_MODE_COMMUN => 'Transports en commun',
    ];

    const TRANSPORT_DISTANCES = [
        AFUP_TRANSPORT_DISTANCE_0 => '< 25 km',
        AFUP_TRANSPORT_DISTANCE_25_50 => 'entre 25 et 50 km',
        AFUP_TRANSPORT_DISTANCE_50_100 => 'entre 50 et 100 km',
        AFUP_TRANSPORT_DISTANCE_100_500 => 'entre 100 et 500 km',
        AFUP_TRANSPORT_DISTANCE_500_1000 => 'entre 500 et 1000 km',
        AFUP_TRANSPORT_DISTANCE_1000 => '> 1000 km',
    ];

    const STATUS_CREATED = AFUP_FORUM_ETAT_CREE;
    const STATUS_CANCELLED = AFUP_FORUM_ETAT_ANNULE;
    const STATUS_ERROR = AFUP_FORUM_ETAT_ERREUR;
    const STATUS_DECLINED = AFUP_FORUM_ETAT_REFUSE;
    const STATUS_PAID = AFUP_FORUM_ETAT_REGLE;
    const STATUS_GUEST = AFUP_FORUM_ETAT_INVITE;
    const STATUS_WAITING = AFUP_FORUM_ETAT_ATTENTE_REGLEMENT;
    const STATUS_CONFIRMED = AFUP_FORUM_ETAT_CONFIRME; // Je ne comprends pas ce que veut dire ce statut @todo check & delete
    const STATUS_PAID_AFTER = AFUP_FORUM_REGLEMENT_A_POSTERIORI; // Je ne comprends pas l'intéret @todo check & delete

    const INVOICE_TODO = AFUP_FORUM_FACTURE_A_ENVOYER;
    const INVOICE_SENT = AFUP_FORUM_FACTURE_ENVOYEE;
    const INVOICE_RECEIVED = AFUP_FORUM_FACTURE_RECUE; // Je ne comprends pas ce que veut dire ce statut ici @todo check & delete

    const TYPE_DAY_1 = AFUP_FORUM_PREMIERE_JOURNEE;
    const TYPE_DAY_2 = AFUP_FORUM_DEUXIEME_JOURNEE;
    const TYPE_2_DAYS = AFUP_FORUM_2_JOURNEES;
    const TYPE_2_DAYS_AFUP = AFUP_FORUM_2_JOURNEES_AFUP;
    const TYPE_2_DAYS_STUDENT = AFUP_FORUM_2_JOURNEES_ETUDIANT;
    const TYPE_2_DAYS_EARLY = AFUP_FORUM_2_JOURNEES_PREVENTE;
    const TYPE_2_DAYS_AFUP_EARLY = AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE;
    const TYPE_2_DAYS_STUDENT_EARLY = AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE;
    const TYPE_2_DAYS_VOUCHER = AFUP_FORUM_2_JOURNEES_COUPON;
    const TYPE_ORGANIZATION = AFUP_FORUM_ORGANISATION;
    const TYPE_SPONSOR = AFUP_FORUM_SPONSOR;
    const TYPE_PRESS = AFUP_FORUM_PRESSE;
    const TYPE_SPEAKER = AFUP_FORUM_CONFERENCIER;
    const TYPE_INVITATION = AFUP_FORUM_INVITATION;
    const TYPE_PROJECT = AFUP_FORUM_PROJET;
    const TYPE_2_DAYS_SPONSOR = AFUP_FORUM_2_JOURNEES_SPONSOR;
    const TYPE_TEACHER = AFUP_FORUM_PROF;
    const TYPE_DAY_1_STUDENT_EARLY = AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE;
    const TYPE_DAY_2_STUDENT_EARLY = AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE;
    const TYPE_DAY_1_STUDENT = AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT;
    const TYPE_DAY_2_STUDENT = AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT;
    const TYPE_AFUP_DAY_EARLY_BIRD = 109;
    const TYPE_AFUP_DAY_CROISIERE = 110;
    const TYPE_AFUP_DAY_LATE = 111;
    const TYPE_AFUP_DAY_CFP_SUBMITTER = 112;
    const TYPE_AFUP_DAY_LIVE_FREE = 113;
    const TYPE_AFUP_DAY_LIVE_SOUTIEN_1 = 114;
    const TYPE_AFUP_DAY_LIVE_SOUTIEN_2 = 115;
    const TYPE_AFUP_DAY_LIVE_SOUTIEN_3 = 116;
    const TYPE_AFUP_DAY_LIVE_SOUTIEN_4 = 117;

    const TYPE_FORUM_PHP_LIVE_FREE = 118;
    const TYPE_FORUM_PHP_LIVE_SOUTIEN_1 = 119;
    const TYPE_FORUM_PHP_LIVE_SOUTIEN_2 = 120;
    const TYPE_FORUM_PHP_LIVE_SOUTIEN_3 = 121;
    const TYPE_FORUM_PHP_LIVE_SOUTIEN_4 = 122;
    const TYPE_FORUM_PHP_LIVE_SOUTIEN_5 = 123;
    const TYPE_FORUM_PHP_LIVE_SOUTIEN_6 = 124;

    const TYPE_AFUP_DAY_2021_LIVE_1 = 125;
    const TYPE_AFUP_DAY_2021_LIVE_2 = 126;
    const TYPE_AFUP_DAY_2021_LIVE_3 = 127;
    const TYPE_AFUP_DAY_2021_LIVE_4 = 128;

    const SPECIAL_PRICE = AFUP_FORUM_SPECIAL_PRICE;

    const PAYMENT_CREDIT_CARD = AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE;
    const PAYMENT_CHEQUE = AFUP_FORUM_REGLEMENT_CHEQUE;
    const PAYMENT_BANKWIRE = AFUP_FORUM_REGLEMENT_VIREMENT;
    const PAYMENT_NONE = AFUP_FORUM_REGLEMENT_AUCUN;
    const PAYMENT_AFTER = AFUP_FORUM_REGLEMENT_A_POSTERIORI; // @todo check & delete je comprends pas

    const DAY_ONE = 'one';
    const DAY_TWO = 'two';

    /**
     * @var int
     */
    private $id;

    private ?\DateTime $date = null;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $voucher;

    /**
     * @var int
     */
    private $ticketTypeId;

    private ?TicketEventType $ticketEventType = null;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $paymentInfo;

    /**
     * @var string
     */
    private $civility;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var bool
     */
    private $companyCitation = false;

    /**
     * @var bool
     */
    private $newsletter = false;

    /**
     * @var bool
     */
    private $optin = false;

    /**
     * @var string
     */
    private $comments;

    /**
     * @var int
     */
    private $status;

    /**
     * @var int
     */
    private $invoiceStatus;

    /**
     * @var int
     */
    private $forumId;

    private ?int $memberType = null;

    private ?int $memberId = null;

    /**
     * @var bool
     */
    private $day1Checkin;

    /**
     * @var bool
     */
    private $day2Checkin;

    /**
     * @var string
     */
    public $tag1;

    /**
     * @var string
     */
    public $tag2;

    /**
     * @var string
     */
    public $tag3;

    /**
     * @var null|string
     */
    protected $specialPriceToken;

    /**
     * @var null|string
     */
    protected $nearestOffice;

    /**
     * @var null|int
     */
    protected $transportMode;

    /**
     * @var null|int
     */
    protected $transportDistance;

    /**
     * @var null|string
     */
    protected $qrCode;

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
    public function setId($id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->propertyChanged('date', $this->date, $date);
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference($reference): self
    {
        $this->propertyChanged('reference', $this->reference, $reference);
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return string
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * @param string $voucher
     */
    public function setVoucher($voucher): self
    {
        $this->propertyChanged('voucher', $this->voucher, $voucher);
        $this->voucher = $voucher;
        return $this;
    }

    /**
     * @return int
     */
    public function getTicketTypeId()
    {
        return $this->ticketTypeId;
    }

    /**
     * @param int $ticketTypeId
     */
    public function setTicketTypeId($ticketTypeId): self
    {
        $this->propertyChanged('ticketTypeId', $this->ticketTypeId, $ticketTypeId);
        $this->ticketTypeId = $ticketTypeId;
        return $this;
    }

    /**
     * @return TicketEventType
     */
    public function getTicketEventType(): ?TicketEventType
    {
        return $this->ticketEventType;
    }

    public function setTicketEventType(TicketEventType $ticketEventType): self
    {
        $this->ticketEventType = $ticketEventType;
        $this->ticketTypeId = $ticketEventType->getTicketTypeId();
        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount): self
    {
        $this->propertyChanged('amount', $this->amount, $amount);
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    /**
     * @param string $paymentInfo
     */
    public function setPaymentInfo($paymentInfo): self
    {
        $this->propertyChanged('paymentInfo', $this->paymentInfo, $paymentInfo);
        $this->paymentInfo = $paymentInfo;
        return $this;
    }

    /**
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param string $civility
     */
    public function setCivility($civility): self
    {
        $this->propertyChanged('civility', $this->civility, $civility);
        $this->civility = $civility;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname): self
    {
        $this->propertyChanged('firstname', $this->firstname, $firstname);
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname): self
    {
        $this->propertyChanged('lastname', $this->lastname, $lastname);
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): self
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber): self
    {
        $this->propertyChanged('phoneNumber', $this->phoneNumber, $phoneNumber);
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCompanyCitation()
    {
        return $this->companyCitation;
    }

    /**
     * @param bool $companyCitation
     */
    public function setCompanyCitation($companyCitation): self
    {
        $this->propertyChanged('companyCitation', $this->companyCitation, $companyCitation);
        $this->companyCitation = $companyCitation;
        return $this;
    }

    /**
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param bool $newsletter
     */
    public function setNewsletter($newsletter): self
    {
        $this->propertyChanged('newsletter', $this->newsletter, $newsletter);
        $this->newsletter = $newsletter;
        return $this;
    }

    /**
     * @return bool
     */
    public function getOptin()
    {
        return $this->optin;
    }

    /**
     * @param bool $optin
     */
    public function setOptin($optin): self
    {
        $this->propertyChanged('optin', $this->optin, $optin);
        $this->optin = $optin;
        return $this;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     */
    public function setComments($comments): self
    {
        $this->propertyChanged('comments', $this->comments, $comments);
        $this->comments = $comments;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status): self
    {
        $this->propertyChanged('status', $this->status, $status);
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getInvoiceStatus()
    {
        return $this->invoiceStatus;
    }

    /**
     * @param int $invoiceStatus
     */
    public function setInvoiceStatus($invoiceStatus): self
    {
        $this->propertyChanged('invoiceStatus', $this->invoiceStatus, $invoiceStatus);
        $this->invoiceStatus = $invoiceStatus;
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
     */
    public function setForumId($forumId): self
    {
        $this->propertyChanged('forumId', $this->forumId, $forumId);
        $this->forumId = $forumId;
        return $this;
    }

    /**
     * @return int
     */
    public function getMemberType(): ?int
    {
        return $this->memberType;
    }

    /**
     * @param int $memberType
     */
    public function setMemberType($memberType): self
    {
        $memberType = (int) $memberType;
        $this->propertyChanged('memberType', $this->memberType, $memberType);

        $this->memberType = $memberType;
        return $this;
    }

    /**
     * @return int
     */
    public function getMemberId(): ?int
    {
        return $this->memberId;
    }

    /**
     * @param int $memberId
     */
    public function setMemberId($memberId): self
    {
        $memberId = (int) $memberId;
        $this->propertyChanged('memberId', $this->memberId, $memberId);

        $this->memberId = $memberId;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDay1Checkin()
    {
        return $this->day1Checkin;
    }

    /**
     * @param bool $day1Checkin
     */
    public function setDay1Checkin($day1Checkin): self
    {
        $this->propertyChanged('day1Checkin', $this->day1Checkin, $day1Checkin);
        $this->day1Checkin = $day1Checkin;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDay2Checkin()
    {
        return $this->day2Checkin;
    }

    /**
     * @param bool $day2Checkin
     */
    public function setDay2Checkin($day2Checkin): self
    {
        $this->propertyChanged('day2Checkin', $this->day2Checkin, $day2Checkin);
        $this->day2Checkin = $day2Checkin;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getTags(): array
    {
        return [$this->tag1, $this->tag2, $this->tag3];
    }

    /**
     * @return null|string
     */
    public function getSpecialPriceToken()
    {
        return $this->specialPriceToken;
    }

    /**
     * @param null|string $specialPriceToken
     */
    public function setSpecialPriceToken($specialPriceToken): self
    {
        $this->propertyChanged('specialPriceToken', $this->specialPriceToken, $specialPriceToken);
        $this->specialPriceToken = $specialPriceToken;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNearestOffice()
    {
        return $this->nearestOffice;
    }

    /**
     * @param null|string $nearestOffice
     *
     * @return $this
     */
    public function setNearestOffice($nearestOffice): self
    {
        $this->propertyChanged('nearestOffice', $this->nearestOffice, $nearestOffice);

        $this->nearestOffice = $nearestOffice;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getTransportMode()
    {
        return $this->transportMode;
    }

    /**
     * @param null|int $transportMode
     *
     * @return $this
     */
    public function setTransportMode($transportMode): self
    {
        $this->propertyChanged('transportMode', $this->transportMode, $transportMode);

        $this->transportMode = $transportMode;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getTransportDistance()
    {
        return $this->transportDistance;
    }

    /**
     * @param null|int $transportDistance
     *
     * @return $this
     */
    public function setTransportDistance($transportDistance): self
    {
        $this->propertyChanged('transportDistance', $this->transportDistance, $transportDistance);

        $this->transportDistance = $transportDistance;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getQrCode()
    {
        return $this->qrCode;
    }

    /**
     * @param null|string $qrCode
     * @return $this
     */
    public function setQrCode($qrCode): self
    {
        $this->propertyChanged('qrCode', $this->qrCode, $qrCode);
        $this->qrCode = $qrCode;

        return $this;
    }
}
