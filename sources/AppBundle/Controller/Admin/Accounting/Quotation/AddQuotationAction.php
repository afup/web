<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use AppBundle\Accounting\Entity\Invoicing;
use AppBundle\Accounting\Entity\InvoicingDetail;
use AppBundle\Accounting\Entity\Repository\InvoicingRepository;
use AppBundle\Accounting\Entity\Repository\ProduitRepository;
use AppBundle\Accounting\Form\QuotationType;
use AppBundle\Accounting\InvoicingNumberGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddQuotationAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingRepository $invoicingRepository,
        private readonly InvoicingNumberGenerator $numberGenerator,
        private readonly ProduitRepository $produitRepository,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(Request $request): Response
    {
        $quotation = $this->init($request->query->getInt('from'));
        $form = $this->createForm(QuotationType::class, $quotation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $quotation->numeroDevis = $this->numberGenerator->generateQuotationNumber();
                foreach ($quotation->details as $detail) {
                    $detail->facture = $quotation;
                }
                $this->invoicingRepository->save($quotation);
                $this->addFlash('success',  'L\'écriture a été ajoutée');
                return $this->redirectToRoute('admin_accounting_quotations_list');
            } catch (\Exception $e) {
                $this->logger->error('Échec de l\'ajout d\'un devis : ' . $e->getMessage(), ['exception' => $e]);
                $this->addFlash('error',  'L\'écriture n\'a pas pu être enregistrée');
            }
        }

        return $this->render('admin/accounting/quotation/add.html.twig', [
            'quotation' => $quotation,
            'form' => $form->createView(),
            'submitLabel' => 'Ajouter',
            'produits' => $this->produitRepository->getAllSortedByReference(),
        ]);
    }

    private function init(int $quotationId): Invoicing
    {
        $baseQuotation = $this->invoicingRepository->getById($quotationId);
        if (!$baseQuotation instanceof Invoicing) {
            $quotation = new Invoicing();
            $quotation->dateDevis = new \DateTime();
            $quotation->idPays = 'FR';

            return $quotation;
        }

        $quotation = new Invoicing();
        $quotation->dateDevis = new \DateTime();
        $quotation->dateFacture = $baseQuotation->dateFacture;
        $quotation->societe = $baseQuotation->societe;
        $quotation->service = $baseQuotation->service;
        $quotation->adresse = $baseQuotation->adresse;
        $quotation->codePostal = $baseQuotation->codePostal;
        $quotation->ville = $baseQuotation->ville;
        $quotation->idPays = $baseQuotation->idPays;
        $quotation->email = $baseQuotation->email;
        $quotation->tvaIntra = $baseQuotation->tvaIntra;
        $quotation->observation = $baseQuotation->observation;
        $quotation->referenceClient1 = $baseQuotation->referenceClient1;
        $quotation->referenceClient2 = $baseQuotation->referenceClient2;
        $quotation->referenceClient3 = $baseQuotation->referenceClient3;
        $quotation->nom = $baseQuotation->nom;
        $quotation->prenom = $baseQuotation->prenom;
        $quotation->telephone = $baseQuotation->telephone;
        $quotation->numeroDevis = '';
        $quotation->numeroFacture = '';

        foreach ($baseQuotation->details as $detail) {
            $clone = new InvoicingDetail();
            $clone->reference = $detail->reference;
            $clone->designation = $detail->designation;
            $clone->quantite = $detail->quantite;
            $clone->prixUnitaire = $detail->prixUnitaire;
            $clone->tva = $detail->tva;
            $quotation->addDetail($clone);
        }

        return $quotation;
    }
}
