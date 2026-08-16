<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use AppBundle\Accounting\Entity\Repository\InvoicingRepository;
use AppBundle\Accounting\Entity\Repository\ProduitRepository;
use AppBundle\Accounting\Form\QuotationType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditQuotationAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingRepository $invoicingRepository,
        private readonly ProduitRepository $produitRepository,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(Request $request): Response
    {
        $quotationId = $request->query->getInt('quotationId');
        $quotation = $this->invoicingRepository->getById($quotationId);
        if ($quotation === null) {
            throw $this->createNotFoundException("Ce devis n'existe pas");
        }

        $form = $this->createForm(QuotationType::class, $quotation, ['actionType' => 'edit']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                foreach ($quotation->details as $detail) {
                    $detail->facture = $quotation;
                }
                $this->invoicingRepository->save($quotation);
                $this->addFlash('success',  'L\'écriture a été modifiée');
                return $this->redirectToRoute('admin_accounting_quotations_list');
            } catch (\Exception $e) {
                $this->logger->error('Échec de la modification d\'un devis : ' . $e->getMessage(), ['exception' => $e]);
                $this->addFlash('error',  'L\'écriture n\'a pas pu être enregistrée');
            }
        }

        return $this->render('admin/accounting/quotation/edit.html.twig', [
            'quotation' => $quotation,
            'form' => $form->createView(),
            'submitLabel' => 'Modifier',
            'produits' => $this->produitRepository->getAllSortedByReference(),
        ]);
    }
}
