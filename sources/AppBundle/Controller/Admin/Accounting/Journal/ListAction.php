<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use AppBundle\Accounting\Entity\Repository\EventRepository;
use AppBundle\Accounting\Entity\Repository\InvoicingPeriodRepository;
use AppBundle\Accounting\Entity\Repository\PaymentRepository;
use AppBundle\Accounting\Entity\Repository\TransactionRepository;
use AppBundle\Accounting\Form\InvoicingPeriodType;
use AppBundle\Accounting\OperationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EventRepository $eventRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly InvoicingPeriodRepository $invoicingPeriodRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $periodId = $request->query->has('periodId') ? $request->query->getInt('periodId') : null;
        $period = $this->invoicingPeriodRepository->getCurrentPeriod($periodId);
        $formPeriod = $this->createForm(InvoicingPeriodType::class, $period);
        $periods = $this->invoicingPeriodRepository->findAll();
        $withReconciled = $request->query->getBoolean('with_reconciled');
        $type = OperationType::tryfrom($request->query->getInt('type'));

        $transactions = $this->transactionRepository->getEntriesPerInvoicingPeriod($period, !$withReconciled, $type->value ?? 0);

        return $this->render('admin/accounting/journal/list.html.twig', [
            'periods' => $periods,
            'periodId' => $period->id,
            'formPeriod' => $formPeriod->createView(),
            'withReconciled' => $withReconciled,
            'type' => $type,
            'categories' => $this->categoryRepository->getAllSortedByName(true),
            'events' => $this->eventRepository->getAllSortedByName(true),
            'paymentTypes' => $this->paymentRepository->getAllSortedByName(),
            'transactions' => $transactions,
        ]);
    }
}
