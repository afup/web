<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee\Entity;

use AppBundle\Association\MemberType;
use AppBundle\Controller\Admin\Membership\MembershipFeePayment;
use AppBundle\Doctrine\Type\UnixTimestampType;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CotisationRepository::class)]
#[ORM\Table(name: 'afup_cotisations')]
class Cotisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(nullable: true, enumType: MemberType::class)]
    public ?MemberType $typePersonne = null;

    #[ORM\Column(nullable: true)]
    public ?int $idPersonne = null;

    #[ORM\Column(nullable: true)]
    public ?float $montant = null;

    #[ORM\Column(nullable: true, enumType: MembershipFeePayment::class)]
    public ?MembershipFeePayment $typeReglement = null;

    #[ORM\Column(nullable: true)]
    public ?string $informationsReglement = null;

    #[ORM\Column(type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $dateDebut = null;

    #[ORM\Column(type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $dateFin = null;

    #[ORM\Column(nullable: true)]
    public ?string $numeroFacture = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $dateFacture = null;

    #[ORM\Column(nullable: true)]
    public ?string $referenceClient = null;

    #[ORM\Column(nullable: true)]
    public ?string $commentaires = null;

    #[ORM\Column(nullable: true)]
    public ?string $token = null;

    #[ORM\Column(nullable: true)]
    public ?int $nombreRelances = null;

    #[ORM\Column(type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $dateDerniereRelance = null;

    // Non persistee - drapeau d'affichage calcule via le Voter, autorise le telechargement de la facture
    public bool $telechargerFacture = false;
}
