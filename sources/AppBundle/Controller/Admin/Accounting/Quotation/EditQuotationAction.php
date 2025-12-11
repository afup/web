<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use AppBundle\Accounting\Form\QuotationType;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditQuotationAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingRepository $invoicingRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $quotationId = $request->query->getInt('quotationId');
        $quotation = $this->invoicingRepository->getQuotationById($quotationId);
        if ($quotation === null) {
            throw new InvalidArgumentException("Ce devis n'existe pas");
        }

        $form = $this->createForm(QuotationType::class, $quotation, ['actionType' => 'edit']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->invoicingRepository->save($quotation);
            $this->addFlash('success',  'L\'écriture a été modifiée');
            return $this->redirectToRoute('admin_accounting_quotations_list');
        }

        return $this->render('admin/accounting/quotation/edit.html.twig', [
            'quotation' => $quotation,
            'form' => $form->createView(),
            'submitLabel' => 'Modifier',
        ]);
    }
}
