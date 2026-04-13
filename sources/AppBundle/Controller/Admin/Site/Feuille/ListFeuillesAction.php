<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Feuille;

use AppBundle\Site\Entity\Repository\FeuilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class ListFeuillesAction
{
    public function __construct(
        private FeuilleRepository $feuilleRepository,
        private Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $fields = ['date', 'nom', 'etat'];

        $sort = $request->query->get('sort', 'date');
        if (in_array($sort, $fields) === false) {
            $sort = 'date';
        }
        $direction = $request->query->get('direction', 'desc');
        $filter = $request->query->get('filter', '');
        $feuilles = $this->feuilleRepository->getAllFeuilles($sort, $direction, $filter);

        return new Response($this->twig->render('admin/site/feuille_list.html.twig', [
            'feuilles' => $feuilles,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
