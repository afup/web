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
            $_SESSION['flash'] = "Une erreur est survenue lors de la suppression de l'écriture";
            $_SESSION['erreur'] = true;
            $this->addFlash('error', "Une erreur est survenue lors de la suppression de l'écriture");
            return $this->redirect('/pages/administration/index.php?page=compta_journal');
        }

        $this->transactionRepository->delete($accounting);
        $this->audit->log("Suppression de l'écriture {$id}");
        $_SESSION['flash'] = "L'écriture a été supprimée";
        $_SESSION['erreur'] = false;
        $this->addFlash('notice', "L'écriture a été supprimée");
        return $this->redirect('/pages/administration/index.php?page=compta_journal');
    }
}
