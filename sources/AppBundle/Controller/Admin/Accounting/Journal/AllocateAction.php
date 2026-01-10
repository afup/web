<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Model\Repository\TransactionRepository;
use AppBundle\Accounting\Model\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AllocateAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
    ) {}

    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $transaction = $this->transactionRepository->get($id);
        $amountToallocate = $request->query->get('amount');
        $totalAmount = 0;

        $lastId = null;
        foreach (explode(';', $amountToallocate) as $amount) {
            $amount = (float) $amount;

            $newTransaction = new Transaction();
            $newTransaction->setOperationId($transaction->getOperationId())
                           ->setAccountId($transaction->getAccountId())
                           ->setCategoryId(26) // A déterminer
                           ->setAmount($amount)
                           ->setAccountingDate($transaction->getAccountingDate())
                           ->setVendorName($transaction->getVendorName())
                           ->setTvaIntra($transaction->getTvaIntra())
                           ->setAmount($amount)
                           ->setDescription($transaction->getDescription())
                           ->setNumber($transaction->getNumber())
                           ->setPaymentTypeId($transaction->getPaymentTypeId())
                           ->setPaymentDate($transaction->getPaymentDate())
                           ->setComment($transaction->getComment())
                           ->setEventId(8) // A déterminer
                           ->setOperationNumber($transaction->getOperationNumber());
            $this->transactionRepository->save($newTransaction);
            $lastId = $newTransaction->getId();
            $totalAmount += $amount;
        }

        $transaction->setAmount($transaction->getAmount() - $totalAmount);
        $this->transactionRepository->save($transaction);

        $_SESSION['flash'] = "L'écriture a été ventilée";
        $_SESSION['erreur'] = false;
        $this->addFlash('notice', "L'écriture a été ventilée");
        return $this->redirect('/pages/administration/index.php?page=compta_journal#journal-ligne-' . $lastId);
    }
}
