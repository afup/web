<?php


namespace AppBundle\Controller\Admin\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Site\Model\Rubrique;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;



class RubriqueController extends Controller
{
    public function index() 
    {
        $rubriques = $this->getRubriqueRepository()->getAllRubriques();
       
        return new Response($this->render('admin/site/rubrique_list.html.twig', [
            'rubriques' => $rubriques,
           /* 'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,*/
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
