<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee;

use AppBundle\Association\MemberType;
use AppBundle\Controller\Admin\Membership\MembershipFeePayment;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;
use DateInterval;
use DateTime;

class MembershipFeeService
{
    public function __construct(private readonly MembershipFeeRepository $membershipFeeRepository) {}

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
        $membershipFee = new MembershipFee();
        $membershipFee
            ->setUserType($typePersonne)
            ->setUserId($idPersonne)
            ->setAmount($montant)
            ->setPaymentType($typeReglement !== null ? MembershipFeePayment::from($typeReglement) : null)
            ->setPaymentDetails($informationsReglement)
            ->setStartDate(new DateTime('@' . $dateDebut))
            ->setEndDate(new DateTime('@' . $dateFin))
            ->setInvoiceNumber($this->membershipFeeRepository->generateInvoiceNumber())
            ->setToken(base64_encode(random_bytes(30)))
            ->setComments($commentaires)
            ->setClientReference($referenceClient)
            ->setInvoiceDate(new \DateTimeImmutable())
        ;
        $this->membershipFeeRepository->save($membershipFee);
    }

    public function isAlreadyPaid(string $cmd): bool
    {
        return $this->membershipFeeRepository->getOneBy(['paymentDetails' => $cmd]) instanceof MembershipFee;
    }

    /**
     * Supprime une cotisation
     *
     * @param $id Identifiant de la cotisation à supprimer
     * @return bool Succès de la suppression
     */
    public function supprimer(int $id): bool
    {
        $cotisation = $this->membershipFeeRepository->get($id);
        $this->membershipFeeRepository->delete($cotisation);
        return true;
    }

    /**
     * Modifie une cotisation
     *
     * @param $id Identifiant de la cotisation à modifier
     * @param $typeReglement Type de règlement (espèces, chèque, virement)
     * @param $informationsReglement Informations concernant le règlement (numéro de chèque, de virement etc.)
     * @return bool Succès de la modification
     */
    public function updatePayment(int $id, int $typeReglement, string $informationsReglement): bool
    {
        return $this->membershipFeeRepository->updatePayment($id, $typeReglement, $informationsReglement) !== false;
    }

    /**
     * Retourne la dernière cotisation d'une personne
     */
    public function getLatestByUserTypeAndId(MemberType $typePersonne, int $idPersonne): ?MembershipFee
    {
        return $this->membershipFeeRepository->getLatestByUserTypeAndId($typePersonne, $idPersonne);
    }

    public function getNextSubscriptionExpiration(?MembershipFee $cotisation = null): DateTime
    {
        $endDate = $cotisation?->getEndDate();
        $endSubscription = $endDate !== null ? (clone $endDate)->setTime(23, 59, 59) : new DateTime();
        $base = $now = new DateTime();

        $year = new DateInterval('P1Y');

        if ($endSubscription > $now) {
            $base = $endSubscription;
        }

        $base->add($year);
        return $base;
    }

    /**
     * Renvoit la cotisation demandée
     */
    public function getByInvoice(string $invoiceId, ?string $token = null): ?MembershipFee
    {
        $criterias = ['invoiceNumber' => $invoiceId];
        if ($token !== null) {
            $criterias['token'] = $token;
        }
        return $this->membershipFeeRepository->getOneBy($criterias);
    }
}
