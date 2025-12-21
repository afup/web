<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\BankAccounts;

use Afup\Site\Comptabilite\Comptabilite;
use AppBundle\Accounting\Form\InvoicingPeriodType;
use AppBundle\Accounting\Model\Repository\AccountRepository;
use AppBundle\Accounting\Model\Repository\InvoicingPeriodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListStatementAction extends AbstractController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly InvoicingPeriodRepository $invoicingPeriodRepository,
        private readonly AccountRepository $accountRepository,
        private readonly Comptabilite $comptabilite,
    ) {}

    public function __invoke(Request $request): Response
    {
        $periodId = $request->query->has('periodId') ? $request->query->getInt('periodId') : null;
        $accountId = $request->query->getInt('accountId', 1);
        $period = $this->invoicingPeriodRepository->getCurrentPeriod($periodId);
        $formPeriod = $this->createForm(InvoicingPeriodType::class, $period);
        $periods = $this->invoicingPeriodRepository->getAll();

        $accounts = $this->accountRepository->getActiveAccounts();
        $start = $period->getStartDate()->format('Y-m-d');
        $end = $period->getEndDate()->format('Y-m-d');
        $statements = $this->comptabilite->obtenirJournalBanque($accountId, $start, $end);
        $subTotal = $this->comptabilite->obtenirSousTotalJournalBanque($start, $end, $accountId);
        $total = $this->comptabilite->obtenirTotalJournalBanque($start, $end, $accountId);

        return new Response($this->twig->render('admin/accounting/bank-accounts/list.html.twig', [
            'periods' => $periods,
            'periodId' => $period->getId(),
            'formPeriod' => $formPeriod->createView(),
            'accounts' => $accounts,
            'accountId' => $accountId,
            'statements' => $statements,
            'subTotal' => $subTotal,
            'total' => $total,
        ]));
    }
}
