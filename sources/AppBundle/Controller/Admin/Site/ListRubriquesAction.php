<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListRubriquesAction
{
    public function __construct(
        private readonly RubriqueRepository $rubriqueRepository,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $fields = ['date', 'nom', 'etat'];

        $sort = $request->query->get('sort', 'nom');
        if (in_array($sort, $fields) === false) {
            $sort = 'nom';
        }
        $direction = $request->query->get('direction', 'asc');
        $filter = $request->query->get('filter', '');
        $rubriques = $this->rubriqueRepository->getAllRubriques($sort, $direction, $filter);

        return new Response($this->twig->render('admin/site/rubrique_list.html.twig', [
            'rubriques' => $rubriques,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
