<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Produit;

use AppBundle\Accounting\Entity\Produit;
use AppBundle\Accounting\Entity\Repository\ProduitRepository;
use AppBundle\Accounting\Form\ProduitType;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddProduitAction extends AbstractController
{
    public function __construct(
        private readonly ProduitRepository $produitRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->produitRepository->save($produit);
            $this->audit->log('Ajout du produit ' . $produit->reference);
            $this->addFlash('notice', 'Le produit a été ajouté');
            return $this->redirectToRoute('admin_accounting_produits_list');
        }

        return $this->render('admin/accounting/produit/form.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
            'formTitle' => 'Ajouter un produit',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
