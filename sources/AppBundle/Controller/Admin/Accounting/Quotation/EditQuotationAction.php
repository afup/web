<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use AppBundle\Accounting\Form\QuotationType;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Accounting\Model\Repository\InvoicingDetailRepository;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditQuotationAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingRepository $invoicingRepository,
        private readonly InvoicingDetailRepository $invoicingDetailRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $quotationId = $request->query->getInt('quotationId');
        $quotation = $this->invoicingRepository->getQuotationById($quotationId);
        if ($quotation === null) {
            throw $this->createNotFoundException("Ce devis n'existe pas");
        }

        $this->cleanLegacyData($quotation);

        $form = $this->createForm(QuotationType::class, $quotation, ['actionType' => 'edit']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $idsToRemove = $this->invoicingDetailRepository->getRowsIdsPerInvoicingId($quotation->getId());
                $existingIds = [];
                $this->invoicingRepository->startTransaction();
                $this->invoicingRepository->save($quotation);
                foreach ($quotation->getDetails() as $detail) {
                    if ($detail->getId() !== null) {
                        $existingIds[] = $detail->getId();
                    }
                    $detail->setInvoicingId($quotation->getId());
                    $this->invoicingDetailRepository->save($detail);
                }

                $idsToRemove = array_diff($idsToRemove, $existingIds);
                if ($idsToRemove) {
                    $this->invoicingDetailRepository->removeRowsPerIds($idsToRemove);
                }
                $this->invoicingRepository->save($quotation);
                $this->invoicingRepository->commit();
                $this->addFlash('success',  'L\'écriture a été modifiée');
                return $this->redirectToRoute('admin_accounting_quotations_list');
            } catch (\Exception $e) {
                $this->invoicingRepository->rollback();
                $this->addFlash('error',  'L\'écriture n\'a pas pu être enregistrée');
            }
        }

        return $this->render('admin/accounting/quotation/edit.html.twig', [
            'quotation' => $quotation,
            'form' => $form->createView(),
            'submitLabel' => 'Modifier',
        ]);
    }

    /**
     * Pour simplifier la modification d'un devis créé avant ma refonte sous symfony, on supprime les lignes vides
     */
    private function cleanLegacyData(Invoicing $quotation): void
    {
        $cleanedDetails = [];
        foreach ($quotation->getDetails() as $detail) {
            if (empty($detail->getDesignation()) && empty($detail->getQuantity()) && empty($detail->getUnitPrice()) && empty($detail->getReference())) {
                $this->invoicingDetailRepository->delete($detail);
            } else {
                $cleanedDetails[] = $detail;
            }
        }
        $quotation->setDetails($cleanedDetails);

    }
}
