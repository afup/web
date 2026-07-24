<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee;

use AppBundle\Association\MemberType;
use AppBundle\Controller\Admin\Membership\MembershipFeePayment;
use AppBundle\MembershipFee\Entity\Cotisation;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use DateInterval;
use DateTime;
use Psr\Clock\ClockInterface;

readonly class MembershipFeeService
{
    public function __construct(
        private CotisationRepository $membershipFeeRepository,
        private ClockInterface $clock,
    ) {}

    public function ajouter(
        MemberType $typePersonne,
        // Identifiant de la personne
        int $idPersonne,
        // Montant de la cotisation (en euros)
        float $montant,
        // Type de règlement (espèces, chèque, virement)
        ?int $typeReglement,
        // Informations concernant le règlement (numéro de chèque, de virement etc.)
        ?string $informationsReglement,
        // Date de début de la cotisation
        int $dateDebut,
        // Date de fin de la cotisation
        int $dateFin,
        // Commentaires concernant la cotisation
        string $commentaires,
        // Référence client à mentionner sur la facture
        ?string $referenceClient = null,
    ): void {
        $cotisation = new Cotisation();
        $cotisation->typePersonne = $typePersonne;
        $cotisation->idPersonne = $idPersonne;
        $cotisation->montant = $montant;
        $cotisation->typeReglement = $typeReglement !== null ? MembershipFeePayment::from($typeReglement) : null;
        $cotisation->informationsReglement = $informationsReglement;
        $cotisation->dateDebut = new DateTime('@' . $dateDebut);
        $cotisation->dateFin = new DateTime('@' . $dateFin);
        $cotisation->numeroFacture = $this->membershipFeeRepository->generateInvoiceNumber();
        $cotisation->token = base64_encode(random_bytes(30));
        $cotisation->commentaires = $commentaires;
        $cotisation->referenceClient = $referenceClient;
        $cotisation->dateFacture = new \DateTimeImmutable();

        $this->membershipFeeRepository->save($cotisation);
    }

    public function isAlreadyPaid(string $cmd): bool
    {
        return $this->membershipFeeRepository->findOneBy(['informationsReglement' => $cmd]) instanceof Cotisation;
    }

    /**
     * Supprime une cotisation
     */
    public function supprimer(int $id): bool
    {
        $cotisation = $this->membershipFeeRepository->find($id);
        $this->membershipFeeRepository->delete($cotisation);
        return true;
    }

    /**
     * Modifie une cotisation
     */
    public function updatePayment(int $id, MembershipFeePayment $typeReglement, string $informationsReglement): bool
    {
        return $this->membershipFeeRepository->updatePayment($id, $typeReglement, $informationsReglement) !== false;
    }

    /**
     * Retourne la dernière cotisation d'une personne
     */
    public function getLatestByUserTypeAndId(MemberType $typePersonne, int $idPersonne): ?Cotisation
    {
        return $this->membershipFeeRepository->getLatestByUserTypeAndId($typePersonne, $idPersonne);
    }

    public function getNextSubscriptionExpiration(?Cotisation $cotisation = null): DateTime
    {
        $now = $this->clock->now();
        $base = clone $now;
        $endDate = $cotisation?->dateFin;

        if ($endDate !== null) {
            // La date de fin est stockée en UTC.
            // On la ramène dans le bon fuseau horaire avant de la modifier.
            $endSubscription = (clone $endDate)
                ->setTimezone($now->getTimezone())
                ->setTime(23, 59, 59);

            if ($endSubscription > $now) {
                $base = $endSubscription;
            }
        }

        $result = $base->add(new DateInterval('P1Y'));

        return new \DateTime(timezone: $result->getTimezone())
            ->setTimestamp($result->getTimestamp());
    }

    /**
     * Renvoit la cotisation demandée
     */
    public function getByInvoice(string $invoiceId, ?string $token = null): ?Cotisation
    {
        $criterias = ['numeroFacture' => $invoiceId];
        if ($token !== null) {
            $criterias['token'] = $token;
        }
        return $this->membershipFeeRepository->findOneBy($criterias);
    }
}
