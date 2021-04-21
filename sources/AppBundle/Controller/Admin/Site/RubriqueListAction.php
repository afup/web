<?php

namespace AppBundle\Controller\Admin\Site;

use AppBundle\Association\Model\Repository\RubriqueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

use Afup\Site\Corporate\Feuilles;
use AppBundle\Association\Model\Rubrique;
use AppBundle\Association\Model\Rubriques;
use AppBundle\Association\Model\Repository\UserRepository;

class RubriqueListAction
{
    /** @var Environment */
    private $twig;

    /** @var RubriqueRepository */
    private $rubriqueRepository;

    public function __construct(RubriqueRepository $rubriqueRepository, Environment $twig) {
        $this->rubriqueRepository = $rubriqueRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {

        
        // Modification des paramètres de tri en fonction des demandes passées en GET
        $sort = $request->query->get('sort', 'name');
        $direction = $request->query->get('direction', 'asc');
        $filter = $request->query->get('filter');

        return new Response($this->twig->render('admin/site/rubrique_list.html.twig', [
            'rubriques' => $this->rubriqueRepository->obtenirListe(),
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
