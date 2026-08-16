<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use AppBundle\Accounting\Entity\Repository\TransactionRepository;
use AppBundle\Accounting\TvaZone;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Table(name: 'compta')]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'idclef', nullable: true)]
    public ?string $idClef = null;

    #[ORM\ManyToOne(targetEntity: Operation::class)]
    #[ORM\JoinColumn(name: 'idoperation', referencedColumnName: 'id', nullable: true)]
    public ?Operation $operation = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'idcategorie', referencedColumnName: 'id', nullable: true)]
    public ?Category $categorie = null;

    #[ORM\Column(name: 'date_ecriture', nullable: true)]
    public ?\DateTime $dateEcriture = null;

    #[ORM\Column(name: 'numero_operation', nullable: true)]
    public ?string $numeroOperation = null;

    #[ORM\Column(name: 'nom_frs', nullable: false)]
    public string $nomFournisseur = '';

    #[ORM\Column(name: 'tva_intra', nullable: true)]
    public ?string $tvaIntra = null;

    #[ORM\Column(name: 'tva_zone', nullable: true, enumType: TvaZone::class)]
    public ?TvaZone $tvaZone = null;

    #[ORM\Column(name: 'montant', type: 'float', nullable: false)]
    public float $montant = 0.0;

    #[ORM\Column(nullable: false)]
    public string $description = '';

    #[ORM\Column(name: 'comment', nullable: true)]
    public ?string $commentaire = null;

    #[ORM\Column(name: 'attachment_required', nullable: false)]
    public bool $justificatifRequis = false;

    #[ORM\Column(name: 'attachment_filename', nullable: true)]
    public ?string $nomJustificatif = null;

    #[ORM\Column(name: 'numero', nullable: false)]
    public string $numero = '';

    #[ORM\ManyToOne(targetEntity: Payment::class)]
    #[ORM\JoinColumn(name: 'idmode_regl', referencedColumnName: 'id', nullable: true)]
    public ?Payment $modeReglement = null;

    #[ORM\Column(name: 'date_regl', nullable: true)]
    public ?\DateTime $dateReglement = null;

    #[ORM\Column(name: 'obs_regl', nullable: true)]
    public ?string $commentaireReglement = '';

    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(name: 'idevenement', referencedColumnName: 'id', nullable: true)]
    public ?Event $evenement = null;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'idcompte', referencedColumnName: 'id', nullable: true)]
    public ?Account $compte = null;

    #[ORM\Column(name: 'montant_ht_soumis_tva_20', type: 'float', nullable: true)]
    public ?float $montantTva20 = null;

    #[ORM\Column(name: 'montant_ht_soumis_tva_10', type: 'float', nullable: true)]
    public ?float $montantTva10 = null;

    #[ORM\Column(name: 'montant_ht_soumis_tva_5_5', type: 'float', nullable: true)]
    public ?float $montantTva5_5 = null;

    #[ORM\Column(name: 'montant_ht_soumis_tva_0', type: 'float', nullable: true)]
    public ?float $montantTva0 = null;

    public function __construct()
    {
        $this->dateEcriture = new \DateTime();
        $this->dateReglement = new \DateTime();
    }
}
