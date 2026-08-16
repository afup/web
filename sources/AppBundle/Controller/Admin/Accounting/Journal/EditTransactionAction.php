<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Entity\Repository\TransactionRepository;
use AppBundle\Accounting\Entity\Transaction;
use AppBundle\Accounting\Form\TransactionType;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditTransactionAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request, int $id): Response
    {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction instanceof Transaction) {
            throw new NotFoundHttpException();
        }
        $nextTransaction = $this->transactionRepository->getNextTransaction($transaction->id);


        $form = $this->createForm(TransactionType::class, $transaction, ['operation' => 'edit', 'nextTransaction' => $nextTransaction]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $passButton = $form->has('pass') ? $form->get('pass') : null;
            if ($passButton instanceof SubmitButton && $passButton->isClicked()) {
                $this->addFlash('notice', 'L\'écriture n\'a pas été modifiée');
                return $this->redirectToRoute('admin_accounting_journal_edit', ['id' => $nextTransaction->id]);
            }

            $this->transactionRepository->save($transaction);
            $this->audit->log("Modification d'une écriture ({$transaction->id})");
            $this->addFlash('notice', 'L\'écriture a été modifiée');

            $submitAndPassButton = $form->has('submitAndPass') ? $form->get('submitAndPass') : null;
            if ($submitAndPassButton instanceof SubmitButton && $submitAndPassButton->isClicked()) {
                return $this->redirectToRoute('admin_accounting_journal_edit', ['id' => $nextTransaction->id]);
            } else {
                return $this->redirect('/admin/accounting/journal/list#L' . $transaction->id);
            }
        }
        return $this->render('admin/accounting/journal/edit.html.twig', [
            'form' => $form->createView(),
            'operation' => 'edit',
            'nextTransaction' => $nextTransaction,
        ]);
    }
}
