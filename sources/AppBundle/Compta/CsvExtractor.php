<?php

declare(strict_types=1);

namespace AppBundle\Compta;

use AppBundle\Accounting\Entity\Repository\AccountRepository;
use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use AppBundle\Accounting\Entity\Repository\EventRepository;
use AppBundle\Accounting\Entity\Repository\OperationRepository;
use AppBundle\Accounting\Entity\Repository\PaymentRepository;
use AppBundle\Accounting\Entity\Repository\RuleRepository;
use AppBundle\Accounting\Entity\Repository\TransactionRepository;
use AppBundle\Accounting\Entity\Transaction;
use AppBundle\Compta\Importer\AutoQualifier;
use AppBundle\Compta\Importer\Importer;

class CsvExtractor
{
    public function __construct(
        private readonly RuleRepository $ruleRepository,
        private readonly TransactionRepository $transactionRepository,
        private readonly OperationRepository $operationRepository,
        private readonly AccountRepository $accountRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function extract(Importer $importer): bool
    {
        if (!$importer->validate()) {
            return false;
        }

        $rules = $this->ruleRepository->findAll();
        $qualifier = new AutoQualifier($rules);

        foreach ($importer->extract() as $operation) {
            $numero_operation = $operation->numeroOperation;
            // On vérife si l'enregistrement existe déjà
            $enregistrement = $this->transactionRepository->findOneBy(['numeroOperation' => $numero_operation]);

            $operationQualified = $qualifier->qualify($operation);
            if (!$enregistrement instanceof Transaction) {
                $transaction = new Transaction();
                $transaction->operation = $this->operationRepository->find($operationQualified['idoperation']);
                $transaction->compte = $this->accountRepository->find($importer->getCompteId());
                $transaction->categorie = $this->categoryRepository->find($operationQualified['categorie']);
                $transaction->dateEcriture = new \DateTime($operationQualified['date_ecriture']);
                $transaction->nomFournisseur = '';
                $transaction->tvaIntra = '';
                $transaction->montant = $operationQualified['montant'];
                $transaction->description = $operationQualified['description'];
                $transaction->numero = '';
                $transaction->modeReglement = $this->paymentRepository->find($operationQualified['idModeReglement']);
                $transaction->commentaireReglement = '';
                $transaction->evenement = $this->eventRepository->find($operationQualified['evenement']);
                $transaction->numeroOperation = $numero_operation;
                $transaction->justificatifRequis = $operationQualified['attachmentRequired'];
                $transaction->montantTva0 = $operationQualified['montant_ht_soumis_tva_0'];
                $transaction->montantTva5_5 = $operationQualified['montant_ht_soumis_tva_5_5'];
                $transaction->montantTva10 = $operationQualified['montant_ht_soumis_tva_10'];
                $transaction->montantTva20 = $operationQualified['montant_ht_soumis_tva_20'];
                $this->transactionRepository->save($transaction);
            } else {
                $modifier = false;
                if ($enregistrement->categorie?->id === AutoQualifier::DEFAULT_CATEGORIE && $operationQualified['categorie'] != AutoQualifier::DEFAULT_CATEGORIE) {
                    $enregistrement->categorie = $this->categoryRepository->find($operationQualified['categorie']);
                    $modifier = true;
                }
                if ($enregistrement->evenement?->id === AutoQualifier::DEFAULT_EVENEMENT && $operationQualified['evenement'] != AutoQualifier::DEFAULT_EVENEMENT) {
                    $enregistrement->evenement = $this->eventRepository->find($operationQualified['evenement']);
                    $modifier = true;
                }
                if ($modifier) {
                    $enregistrement->compte = $this->accountRepository->find($importer->getCompteId());
                    $enregistrement->justificatifRequis = $operationQualified['attachmentRequired'];
                    $this->transactionRepository->save($enregistrement);
                }
            }
        }

        return true;
    }
}
