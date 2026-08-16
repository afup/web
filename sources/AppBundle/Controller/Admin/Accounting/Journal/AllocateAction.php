<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use AppBundle\Accounting\Entity\Repository\EventRepository;
use AppBundle\Accounting\Entity\Repository\TransactionRepository;
use AppBundle\Accounting\Entity\Transaction;
use AppBundle\Compta\Importer\AutoQualifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AllocateAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction instanceof Transaction) {
            throw $this->createNotFoundException();
        }

        $amountToallocate = $request->query->get('amount');
        $totalAmount = 0;

        $undeterminedCategory = $this->categoryRepository->find(AutoQualifier::DEFAULT_CATEGORIE);
        $undeterminedEvent = $this->eventRepository->find(AutoQualifier::DEFAULT_EVENEMENT);

        $lastId = null;
        foreach (explode(';', (string) $amountToallocate) as $amount) {
            $amount = (float) $amount;

            $newTransaction = new Transaction();
            $newTransaction->operation = $transaction->operation;
            $newTransaction->compte = $transaction->compte;
            $newTransaction->categorie = $undeterminedCategory;
            $newTransaction->montant = $amount;
            $newTransaction->dateEcriture = $transaction->dateEcriture;
            $newTransaction->nomFournisseur = $transaction->nomFournisseur;
            $newTransaction->tvaIntra = $transaction->tvaIntra;
            $newTransaction->description = $transaction->description;
            $newTransaction->numero = $transaction->numero;
            $newTransaction->modeReglement = $transaction->modeReglement;
            $newTransaction->dateReglement = $transaction->dateReglement;
            $newTransaction->commentaire = $transaction->commentaire;
            $newTransaction->evenement = $undeterminedEvent;
            $newTransaction->numeroOperation = $transaction->numeroOperation;
            $this->transactionRepository->save($newTransaction);
            $lastId = $newTransaction->id;
            $totalAmount += $amount;
        }

        $transaction->montant -= $totalAmount;
        $this->transactionRepository->save($transaction);

        $this->addFlash('notice', "L'écriture a été ventilée");
        return $this->redirect('/admin/accounting/journal/list#journal-ligne-' . $lastId);
    }
}
