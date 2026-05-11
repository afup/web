<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Produit;

use AppBundle\Accounting\Entity\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class ListProduitAction
{
    public function __construct(
        private ProduitRepository $produitRepository,
        private Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $produits = $this->produitRepository->getAllSortedByReference();

        return new Response($this->twig->render('admin/accounting/produit/list.html.twig', [
            'produits' => $produits,
        ]));
    }
}
