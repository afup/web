<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use Afup\Site\Comptabilite\Facture;
use AppBundle\Accounting\Form\QuotationType;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Accounting\Model\InvoicingDetail;
use AppBundle\Accounting\Model\Repository\InvoicingDetailRepository;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddQuotationAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingRepository $invoicingRepository,
        private readonly Facture $facture,
        private readonly InvoicingDetailRepository $invoicingDetailRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $quotation = $this->init($request->query->getInt('from'));
        $form = $this->createForm(QuotationType::class, $quotation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->invoicingRepository->startTransaction();
                $quotation->setQuotationNumber($this->facture->genererNumeroDevis());
                $this->invoicingRepository->save($quotation);
                foreach ($quotation->getDetails() as $detail) {
                    $detail->setInvoicingId($quotation->getId());
                    $this->invoicingDetailRepository->save($detail);
                }
                $this->invoicingRepository->commit();
                $this->addFlash('success',  'L\'écriture a été ajoutée');
                return $this->redirectToRoute('admin_accounting_quotations_list');
            } catch (\Exception $e) {
                $this->invoicingRepository->rollback();
                $this->addFlash('error',  'L\'écriture n\'a pas pu être enregistrée');
            }
        }

        return $this->render('admin/accounting/quotation/add.html.twig', [
            'quotation' => $quotation,
            'form' => $form->createView(),
            'submitLabel' => 'Ajouter',
        ]);
    }

    private function init(int $quotationId)
    {
        $baseQuotation = $this->invoicingRepository->get($quotationId);
        if (!$baseQuotation instanceof Invoicing) {
            $quotation =  new Invoicing();
            $quotation->setQuotationDate(new \DateTime());
            $quotation->setCountryId('FR');
            $quotation->setDetails([new InvoicingDetail()]);

            return $quotation;
        }

        $quotation = new Invoicing();
        $quotation->setQuotationDate(new \DateTime());
        $quotation->setInvoiceDate($baseQuotation->getInvoiceDate());
        $quotation->setCompany($baseQuotation->getCompany());
        $quotation->setService($baseQuotation->getService());
        $quotation->setAddress($baseQuotation->getAddress());
        $quotation->setZipcode($baseQuotation->getZipcode());
        $quotation->setCity($baseQuotation->getCity());
        $quotation->setCountryId($baseQuotation->getCountryId());
        $quotation->setEmail($baseQuotation->getEmail());
        $quotation->setTvaIntra($baseQuotation->getTvaIntra());
        $quotation->setObservation($baseQuotation->getObservation());
        $quotation->setRefClt1($baseQuotation->getRefClt1());
        $quotation->setRefClt2($baseQuotation->getRefClt2());
        $quotation->setRefClt3($baseQuotation->getRefClt3());
        $quotation->setLastname($baseQuotation->getLastname());
        $quotation->setFirstname($baseQuotation->getFirstname());
        $quotation->setPhone($baseQuotation->getPhone());
        $quotation->setQuotationNumber('');
        $quotation->setInvoiceNumber('');
        $quotation->setDetails($baseQuotation->getDetails());

        return $quotation;
    }
}
