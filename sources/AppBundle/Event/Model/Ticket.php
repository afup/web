<?php

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

    /**
     * @var \DateTime
     */
    private $date;

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

    /**
     * @var TicketEventType
     */
    private $ticketEventType;

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

    /**
     * @var int
     */
    private $memberType;

    /**
     * @var int
     */
    private $memberId;

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
     * @return Ticket
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Ticket
     */
    public function setDate(\DateTime $date)
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
     * @return Ticket
     */
    public function setReference($reference)
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
     * @return Ticket
     */
    public function setVoucher($voucher)
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
     * @return Ticket
     */
    public function setTicketTypeId($ticketTypeId)
    {
        $this->propertyChanged('ticketTypeId', $this->ticketTypeId, $ticketTypeId);
        $this->ticketTypeId = $ticketTypeId;
        return $this;
    }

    /**
     * @return TicketEventType
     */
    public function getTicketEventType()
    {
        return $this->ticketEventType;
    }

    /**
     * @param TicketEventType $ticketEventType
     * @return Ticket
     */
    public function setTicketEventType(TicketEventType $ticketEventType)
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
     * @return Ticket
     */
    public function setAmount($amount)
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
     * @return Ticket
     */
    public function setPaymentInfo($paymentInfo)
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
     * @return Ticket
     */
    public function setCivility($civility)
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
     * @return Ticket
     */
    public function setFirstname($firstname)
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
     * @return Ticket
     */
    public function setLastname($lastname)
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
     * @return Ticket
     */
    public function setEmail($email)
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
     * @return Ticket
     */
    public function setPhoneNumber($phoneNumber)
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
     * @return Ticket
     */
    public function setCompanyCitation($companyCitation)
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
     * @return Ticket
     */
    public function setNewsletter($newsletter)
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
     * @return Ticket
     */
    public function setOptin($optin)
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
     * @return Ticket
     */
    public function setComments($comments)
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
     * @return Ticket
     */
    public function setStatus($status)
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
     * @return Ticket
     */
    public function setInvoiceStatus($invoiceStatus)
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
     * @return Ticket
     */
    public function setForumId($forumId)
    {
        $this->propertyChanged('forumId', $this->forumId, $forumId);
        $this->forumId = $forumId;
        return $this;
    }

    /**
     * @return int
     */
    public function getMemberType()
    {
        return $this->memberType;
    }

    /**
     * @param int $memberType
     * @return Ticket
     */
    public function setMemberType($memberType)
    {
        $memberType = (int) $memberType;
        $this->propertyChanged('memberType', $this->memberType, $memberType);

        $this->memberType = $memberType;
        return $this;
    }

    /**
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @param int $memberId
     * @return Ticket
     */
    public function setMemberId($memberId)
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
     * @return Ticket
     */
    public function setDay1Checkin($day1Checkin)
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
     * @return Ticket
     */
    public function setDay2Checkin($day2Checkin)
    {
        $this->propertyChanged('day2Checkin', $this->day2Checkin, $day2Checkin);
        $this->day2Checkin = $day2Checkin;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return array
     */
    public function getTags()
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
     *
     * @return Ticket
     */
    public function setSpecialPriceToken($specialPriceToken)
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
    public function setNearestOffice($nearestOffice)
    {
        $this->propertyChanged('nearestOffice', $this->nearestOffice, $nearestOffice);

        $this->nearestOffice = $nearestOffice;

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
    public function setQrCode($qrCode)
    {
        $this->propertyChanged('qrCode', $this->qrCode, $qrCode);
        $this->qrCode = $qrCode;

        return $this;
    }
}
