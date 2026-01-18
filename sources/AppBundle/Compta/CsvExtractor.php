<?php

declare(strict_types=1);

namespace AppBundle\Compta;

use AppBundle\Accounting\Entity\Repository\RuleRepository;
use AppBundle\Accounting\Model\Repository\TransactionRepository;
use AppBundle\Accounting\Model\Transaction;
use AppBundle\Compta\Importer\AutoQualifier;
use AppBundle\Compta\Importer\Importer;

class CsvExtractor
{
    public function __construct(
        private readonly RuleRepository $ruleRepository,
        private readonly TransactionRepository $transactionRepository,
    ) {}

    public function extract(Importer $importer)
    {
        if (!$importer->validate()) {
            return false;
        }

        $rules = $this->ruleRepository->findAll();
        $qualifier = new AutoQualifier($rules);

        foreach ($importer->extract() as $operation) {
            $numero_operation = $operation->numeroOperation;
            // On vérife si l'enregistrement existe déjà
            $enregistrement = $this->transactionRepository->getOneBy(['operationNumber' => $numero_operation]);

            $operationQualified = $qualifier->qualify($operation);
            if (!$enregistrement instanceof Transaction) {
                $transaction = new Transaction();
                $transaction->setOperationId($operationQualified['idoperation'])
                            ->setAccountId($importer->getCompteId())
                            ->setCategoryId($operationQualified['categorie'])
                            ->setAccountingDate(new \DateTime($operationQualified['date_ecriture']))
                            ->setVendorName('')
                            ->setTvaIntra('')
                            ->setAmount($operationQualified['montant'])
                            ->setDescription($operationQualified['description'])
                            ->setNumber('')
                            ->setPaymentTypeId($operationQualified['idModeReglement'])
                            ->setAccountingDate(new \DateTime($operationQualified['date_ecriture']))
                            ->setPaymentComment('')
                            ->setEventId($operationQualified['evenement'])
                            ->setOperationNumber($operationQualified['numero_operation'] ?? null)
                            ->setAttachmentRequired($operationQualified['attachmentRequired'])
                            ->setAmountTva0($operationQualified['montant_ht_soumis_tva_0'])
                            ->setAmountTva55($operationQualified['montant_ht_soumis_tva_5_5'])
                            ->setAmountTva10($operationQualified['montant_ht_soumis_tva_10'])
                            ->setAmountTva20($operationQualified['montant_ht_soumis_tva_20']);
                $this->transactionRepository->save($transaction);
            } else {
                $modifier = false;
                if ($enregistrement->getCategoryId() == AutoQualifier::DEFAULT_CATEGORIE && $operationQualified['categorie'] != AutoQualifier::DEFAULT_CATEGORIE) {
                    $enregistrement->setCategoryId($operationQualified['categorie']);
                    $modifier = true;
                }
                if ($enregistrement->getEventId() == AutoQualifier::DEFAULT_EVENEMENT && $operationQualified['evenement'] != AutoQualifier::DEFAULT_EVENEMENT) {
                    $enregistrement->setEventId($operationQualified['evenement']);
                    $modifier = true;
                }
                if ($modifier) {
                    $enregistrement->setAccountId($importer->getCompteId())
                                   ->setAttachmentRequired($operationQualified['attachmentRequired']);
                    $this->transactionRepository->save($enregistrement);
                }
            }
        }

        return true;
    }
}
