<?php

namespace AppBundle\Controller\Admin\Site;

use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Exception;

class ListRubriquesAction
{
    /** @var Environment */
    private $twig;

    /** @var RubriqueRepository */
    private $rubriqueRepository;
    
    public function __construct(  RubriqueRepository $rubriqueRepository, Environment $twig)
    {
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request) 
    {     
        $champs = '*';
        $sort = $request->query->get('sort') ? $GLOBALS['AFUP_DB']->echapper($request->query->get('sort'))  : 'nom';
        $direction = $request->query->get('direction') ? $GLOBALS['AFUP_DB']->echapper($request->query->get('direction')) :'desc';
        $filter = $request->query->get('filter') ? $request->query->get('filter') : null;
        $rubriques = $this->rubriqueRepository->getAllRubriques($champs, $sort, $direction, $filter);
        return new Response($this->twig->render('admin/site/rubrique_list.html.twig', [
            'rubriques' => $rubriques,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }    
}
