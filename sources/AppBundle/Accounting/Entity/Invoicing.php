<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity;

use Afup\Site\Utils\Utils;
use AppBundle\Accounting\Entity\Repository\InvoicingRepository;
use AppBundle\Accounting\InvoicingCurrency;
use AppBundle\Accounting\InvoicingPaymentStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoicingRepository::class)]
#[ORM\Table(name: 'afup_compta_facture')]
class Invoicing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'date_devis', nullable: true)]
    public ?\DateTime $dateDevis = null;

    #[ORM\Column(name: 'numero_devis', nullable: true)]
    public ?string $numeroDevis = null;

    #[ORM\Column(name: 'date_facture', nullable: true)]
    public ?\DateTime $dateFacture = null;

    #[ORM\Column(name: 'numero_facture', nullable: true)]
    public ?string $numeroFacture = null;

    #[ORM\Column(name: 'societe', length: 50, nullable: false)]
    public string $societe = '';

    #[ORM\Column(name: 'service', length: 50, nullable: false)]
    public string $service = '';

    #[ORM\Column(name: 'adresse', type: 'text', nullable: false)]
    public string $adresse = '';

    #[ORM\Column(name: 'code_postal', length: 10, nullable: false)]
    public string $codePostal = '';

    #[ORM\Column(name: 'ville', length: 50, nullable: false)]
    public string $ville = '';

    #[ORM\Column(name: 'id_pays', length: 10, nullable: false)]
    public string $idPays = '';

    #[ORM\Column(length: 100, nullable: false)]
    public string $email = '';

    #[ORM\Column(name: 'tva_intra', length: 20, nullable: true)]
    public ?string $tvaIntra = null;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $observation = '';

    #[ORM\Column(name: 'ref_clt1', length: 50, nullable: false)]
    public string $referenceClient1 = '';

    #[ORM\Column(name: 'ref_clt2', length: 50, nullable: false)]
    public string $referenceClient2 = '';

    #[ORM\Column(name: 'ref_clt3', length: 50, nullable: false)]
    public string $referenceClient3 = '';

    #[ORM\Column(name: 'nom', length: 50, nullable: false)]
    public string $nom = '';

    #[ORM\Column(name: 'prenom', length: 50, nullable: false)]
    public string $prenom = '';

    #[ORM\Column(name: 'tel', length: 30, nullable: false)]
    public string $telephone = '';

    #[ORM\Column(name: 'etat_paiement', nullable: false, enumType: InvoicingPaymentStatus::class)]
    public InvoicingPaymentStatus $etatPaiement = InvoicingPaymentStatus::Waiting;

    #[ORM\Column(name: 'date_paiement', nullable: true)]
    public ?\DateTime $datePaiement = null;

    #[ORM\Column(name: 'devise_facture', nullable: true, enumType: InvoicingCurrency::class)]
    public ?InvoicingCurrency $deviseFacture = null;

    /** @var Collection<int, InvoicingDetail> */
    #[Assert\Valid]
    #[ORM\OneToMany(targetEntity: InvoicingDetail::class, mappedBy: 'facture', cascade: ['persist', 'remove'], orphanRemoval: true)]
    public Collection $details;

    // Non persistee - total calcule depuis les lignes de detail, alimente par les requetes de listing
    public ?float $prix = null;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    public function addDetail(InvoicingDetail $detail): void
    {
        $this->details->add($detail);
        $detail->facture = $this;
    }

    public function getPaymentUrlRef(): string
    {
        if (empty($this->numeroFacture)) {
            return '';
        }

        return urlencode(Utils::cryptFromText($this->id));
    }
}
