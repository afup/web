<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Produit;

use AppBundle\Accounting\Entity\Produit;
use AppBundle\Accounting\Entity\Repository\ProduitRepository;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteProduitAction extends AbstractController
{
    public function __construct(
        private readonly ProduitRepository $produitRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request, int $id): Response
    {
        $produit = $this->produitRepository->find($id);
        if (!$produit instanceof Produit) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du produit');
            return $this->redirectToRoute('admin_accounting_produits_list');
        }

        $this->produitRepository->delete($produit);
        $this->audit->log('Suppression du produit ' . $produit->reference);
        $this->addFlash('notice', 'Le produit a été supprimé');
        return $this->redirectToRoute('admin_accounting_produits_list');
    }
}
