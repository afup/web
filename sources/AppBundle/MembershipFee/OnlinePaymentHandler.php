<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee;

use AppBundle\Association\MemberType;
use AppBundle\Controller\Admin\Membership\MembershipFeePayment;
use AppBundle\MembershipFee\Model\MembershipFee;

class OnlinePaymentHandler
{
    public function __construct(private readonly MembershipFeeService $membershipFeeService) {}

    /**
     * @return array{type: int, id: int}
     */
    public function getAccountFromCmd(string $cmd): array
    {
        $arr = explode('-', $cmd, 5);
        // Depuis une facture : $cmd=FCOTIS-2023-202
        if (3 === count($arr)) {
            return ['type' => MemberType::MemberCompany->value, 'id' => (int) $arr[2]];
        }

        // Depuis une cotisation : $cmd=C2023-211120232237-0-5-PAUL-431
        [$ref, $date, $memberType, $memberId, $stuff] = $arr;

        return ['type' => (int) $memberType, 'id' => (int) $memberId];
    }

    public function validerReglementEnLigne(string $cmd, float $total, string $autorisation, string $transaction): void
    {
        $reference = substr($cmd, 0, strlen($cmd) - 4);
        $verif = substr($cmd, strlen($cmd) - 3, strlen($cmd));

        if (str_starts_with($cmd, 'F')) {
            // This is an invoice ==> we dont have to create a new cotisation, just update the existing one
            $invoiceNumber = substr($cmd, 1);
            $cotisation = $this->membershipFeeService->getByInvoice($invoiceNumber);

            $this->membershipFeeService->updatePayment(
                $cotisation->getId(),
                MembershipFeePayment::OnlinePayment->value,
                "autorisation : " . $autorisation . " / transaction : " . $transaction,
            );
        } elseif (substr(md5($reference), -3) === strtolower($verif) && !$this->membershipFeeService->isAlreadyPaid($cmd)) {
            [$ref, $date, $typePersonne, $idPersonne, $reste] = explode('-', $cmd, 5);
            $dateDebut = mktime(0, 0, 0, (int) substr($date, 2, 2), (int) substr($date, 0, 2), (int) substr($date, 4, 4));

            $cotisation = $this->membershipFeeService->getLatestByUserTypeAndId(MemberType::from((int) $typePersonne), (int) $idPersonne);
            $dateFinPrecedente = !$cotisation instanceof MembershipFee ? 0 : $cotisation->getEndDate()->getTimestamp();

            if ($dateFinPrecedente > 0) {
                $dateDebut = strtotime('+1day', $dateFinPrecedente);
            }

            $dateFin = $this->membershipFeeService->getNextSubscriptionExpiration($cotisation)->getTimestamp();
            $this->membershipFeeService->ajouter(
                MemberType::from((int) $typePersonne),
                (int) $idPersonne,
                $total,
                MembershipFeePayment::OnlinePayment->value,
                $cmd,
                $dateDebut,
                $dateFin,
                "autorisation : " . $autorisation . " / transaction : " . $transaction,
            );
        }
    }
}
