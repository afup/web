<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Form\TransactionType;
use AppBundle\Accounting\Model\Repository\TransactionRepository;
use AppBundle\Accounting\Model\Transaction;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddTransactionAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $transaction = new Transaction();
        $transaction->setAccountId(1); // Compte courant
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->transactionRepository->save($transaction);
            $this->audit->log("Ajout d'une écriture");
            $this->addFlash('notice', "L'écriture a été ajoutée");
            return $this->redirect('/admin/accounting/journal/list#L' . $transaction->getId());
        }

        return $this->render('admin/accounting/journal/add.html.twig', [
            'form' => $form->createView(),
            'operation' => 'add',
        ]);
    }
}
