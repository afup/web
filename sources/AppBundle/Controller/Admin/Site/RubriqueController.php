<?php


namespace AppBundle\Controller\Admin\Site;
use AppBundle\Controller\SiteBaseController;
use AppBundle\Site\Model\Rubrique;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class RubriqueController extends SiteBaseController
{
    public function index() 
    {
        $champs = 'id, date, nom, etat';
        $filter='';
        $sort = 'nom'; 
        $direction = 'desc';
        $rubriques = $this->getRubriqueRepository()->getAllRubriques($champs);
       var_dump($rubriques);
        return new Response($this->render('admin/site/rubrique_list.html.twig', [
            'rubriques' => $rubriques,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }

     /**
     * @return RubriqueRepository
     */
    private function getRubriqueRepository()
    {
        return $this->get('ting')->get(RubriqueRepository::class);
    }
    
    
}
