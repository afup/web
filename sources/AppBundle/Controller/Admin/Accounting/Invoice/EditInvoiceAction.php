<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Invoice;

use AppBundle\Accounting\Form\InvoiceType;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditInvoiceAction extends AbstractController
{
    public function __construct(private readonly InvoicingRepository $invoicingRepository) {}

    public function __invoke(Request $request): Response
    {
        $invoiceId = $request->query->getInt('invoiceId');
        $invoice = $this->invoicingRepository->getById($invoiceId);
        if (!$invoice instanceof Invoicing) {
            throw $this->createNotFoundException("Cette facture n'existe pas");
        }

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->invoicingRepository->save($invoice);
                $this->addFlash('success', 'L\'écriture a été modifiée');
                return $this->redirectToRoute('admin_accounting_invoices_list');
            } catch (\Exception) {
                $this->invoicingRepository->rollback();
                $this->addFlash('error', 'L\'écriture n\'a pas pu être enregistrée');
            }
        }

        return $this->render('admin/accounting/invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form->createView(),
            'submitLabel' => 'Modifier',
        ]);
    }
}
