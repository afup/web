<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Model\Repository\TransactionRepository;
use AppBundle\Accounting\Model\Transaction;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request, int $id): Response
    {
        $accounting = $this->transactionRepository->get($id);
        if (!$accounting instanceof Transaction) {
            $this->addFlash('error', "Une erreur est survenue lors de la suppression de l'écriture");
            return $this->redirectToRoute('admin_accounting_journal_list');
        }

        $this->transactionRepository->delete($accounting);
        $this->audit->log("Suppression de l'écriture {$id}");
        $this->addFlash('notice', "L'écriture a été supprimée");
        return $this->redirectToRoute('admin_accounting_journal_list');
    }
}
