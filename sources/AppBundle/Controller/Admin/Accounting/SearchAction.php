<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting;

use AppBundle\Accounting\Form\SearchType;
use AppBundle\Accounting\SearchResultProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchAction extends AbstractController
{
    public function __construct(private readonly SearchResultProvider $searchResultProvider) {}

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(SearchType::class);

        $data = [];
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $query = $form->getData()['query'] ?? '';
            $data['results'] = $this->searchResultProvider->getResultsForQuery($query);
        }

        return $this->render('admin/accounting/search.html.twig', [
            'form' => $form->createView(),
        ] + $data);
    }
}
