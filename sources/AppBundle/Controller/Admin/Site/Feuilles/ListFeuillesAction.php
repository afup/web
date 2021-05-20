<?php

namespace AppBundle\Controller\Admin\Site\Feuilles;

use AppBundle\Site\Model\Repository\FeuilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListFeuillesAction
{
    /** @var Environment */
    private $twig;

    /** @var FeuilleRepository */
    private $feuilleRepository;

    public function __construct(FeuilleRepository $feuilleRepository, Environment $twig)
    {
        $this->feuilleRepository = $feuilleRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $fields = ['date', 'nom', 'etat', 'position'];

        $sort = $request->query->get('sort', 'nom');
        if (in_array($sort, $fields) === false) {
            $sort = 'nom';
        }
        $direction = $request->query->get('direction', 'asc');
        $filter = $request->query->get('filter');
        $feuilles = $this->feuilleRepository->getAllFeuilles($sort, $direction, $filter);

        return new Response($this->twig->render('admin/site/feuille_list.html.twig', [
            'feuilles' => $feuilles,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
