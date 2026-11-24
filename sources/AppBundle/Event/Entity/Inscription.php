<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Association\Genre;
use AppBundle\Doctrine\Type\UnixTimestampType;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Enum\DistanceTransport;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\InscriptionFacturation;
use AppBundle\Event\Enum\ModeTransport;
use AppBundle\Event\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Inscription (billet) d'une personne à un évènement.
 *
 * Les formulaires d'achat contiennent des billets vides non soumis : les champs texte
 * obligatoires y utilisent "empty_data" pour rester des chaînes.
 */
#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
#[ORM\Table(name: 'afup_inscription_forum')]
#[Assert\LoggedInMember(groups: ['personal'])]
#[Assert\PublicTicket(groups: ['not_logged_in'])]
#[Assert\AvailableTicket]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(type: UnixTimestampType::NAME, nullable: false)]
    public \DateTime $date;

    #[ORM\Column(nullable: false)]
    public string $reference = '';

    #[ORM\Column(name: 'coupon', nullable: true)]
    public ?string $voucher = null;

    #[ORM\Column(name: 'type_inscription', nullable: false)]
    public int $ticketTypeId = 0;

    #[ORM\Column(name: 'montant', nullable: false)]
    public float $amount = 0.0;

    #[ORM\Column(name: 'informations_reglement', nullable: true)]
    public ?string $paymentInfo = null;

    #[ORM\Column(nullable: true, enumType: Genre::class)]
    public ?Genre $genre = null;

    #[ORM\Column(name: 'nom', nullable: false)]
    public string $lastname = '';

    #[ORM\Column(name: 'prenom', nullable: false)]
    public string $firstname = '';

    #[ORM\Column(nullable: false)]
    #[Email]
    public string $email = '';

    #[ORM\Column(name: 'telephone', nullable: true)]
    public ?string $phoneNumber = null;

    #[ORM\Column(name: 'citer_societe', nullable: true)]
    public ?bool $companyCitation = false;

    #[ORM\Column(name: 'newsletter_afup', nullable: true)]
    public ?bool $newsletter = false;

    #[ORM\Column(name: 'mail_partenaire', nullable: false)]
    public bool $optin = false;

    #[ORM\Column(name: 'commentaires', type: 'text', nullable: true)]
    public ?string $comments = null;

    #[ORM\Column(name: 'etat', nullable: false, enumType: InscriptionEtat::class)]
    public InscriptionEtat $status = InscriptionEtat::Creee;

    #[ORM\Column(name: 'facturation', nullable: false, enumType: InscriptionFacturation::class)]
    public InscriptionFacturation $invoiceStatus = InscriptionFacturation::AEnvoyer;

    /**
     * Identifiant de l'évènement (Event est encore un modèle Ting)
     */
    #[ORM\Column(name: 'id_forum', nullable: false)]
    public int $forumId = 0;

    #[ORM\Column(name: 'id_member', nullable: true)]
    public ?int $memberId = null;

    #[ORM\Column(name: 'member_type', nullable: true)]
    public ?int $memberType = null;

    #[ORM\Column(name: 'presence_day1', nullable: true)]
    public ?bool $day1Checkin = null;

    #[ORM\Column(name: 'presence_day2', nullable: true)]
    public ?bool $day2Checkin = null;

    #[ORM\Column(name: 'special_price_token', nullable: true)]
    public ?string $specialPriceToken = null;

    #[ORM\Column(name: 'nearest_office', nullable: true)]
    public ?string $nearestOffice = null;

    #[ORM\Column(name: 'transport_mode', nullable: true, enumType: ModeTransport::class)]
    public ?ModeTransport $transportMode = null;

    #[ORM\Column(name: 'transport_distance', nullable: true, enumType: DistanceTransport::class)]
    public ?DistanceTransport $transportDistance = null;

    #[ORM\Column(name: 'qr_code', nullable: true)]
    public ?string $qrCode = null;

    // Tags saisis lors de l'achat, stockés dans les commentaires : champs non mappés
    public ?string $tag1 = null;
    public ?string $tag2 = null;
    public ?string $tag3 = null;

    // Tarif choisi : champ non mappé, renseigné par le formulaire d'achat
    // ou par InscriptionRepository::getByReferenceWithTicketEventType()
    public ?TicketEventType $ticketEventType = null;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function __clone()
    {
        $this->date = clone $this->date;
    }

    public function setTicketEventType(?TicketEventType $ticketEventType): void
    {
        $this->ticketEventType = $ticketEventType;

        if ($ticketEventType instanceof TicketEventType) {
            $this->ticketTypeId = $ticketEventType->ticketTypeId;
        }
    }

    public function getLabel(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return array{?string, ?string, ?string}
     */
    public function getTags(): array
    {
        return [$this->tag1, $this->tag2, $this->tag3];
    }
}
