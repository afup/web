<?php


namespace AppBundle\Controller\Admin\Site;
use AppBundle\Controller\SiteBaseController;
use AppBundle\Site\Model\Rubrique;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Site\Form\RubriqueEditFormData;
use AppBundle\Site\Form\RubriqueFormDataFactory;
use AppBundle\Site\Form\RubriqueType;
use Symfony\Component\Form\FormFactoryInterface;


class RubriqueController extends SiteBaseController
{

    /** @var FormFactoryInterface */
    private $formFactory;
    private $rubriqueFormDataFactory;
    private $rubriqueEditFormData;
    private $rubriqueRepository;

    public function __construct(
        FormFactoryInterface $formFactory, 
        RubriqueFormDataFactory  $rubriqueFormDataFactory,  
        RubriqueRepository $rubriqueRepository,
        RubriqueEditFormData $rubriqueEditFormData
    )
    {
        $this->formFactory = $formFactory;
        $this->rubriqueFormDataFactory = $rubriqueFormDataFactory;
        $this->rubriqueEditFormData = $rubriqueEditFormData;
        $this->rubriqueRepository =  $rubriqueRepository;
    }

    public function listRubriques(Request $request) 
    {     
        //$champs = 'id, date, nom, etat';
        $champs = '*';
        $sort = $request->query->get('sort') ? $request->query->get('sort') : 'nom';
        $direction = $request->query->get('direction') ? $request->query->get('direction') :'desc';
        $filter = $request->query->get('filter') ? $request->query->get('filter') : null;
        $rubriques = $this->getRubriqueRepository()->getAllRubriques($champs, $sort, $direction, $filter);
        
        return new Response($this->render('admin/site/rubrique_list.html.twig', [
            'rubriques' => $rubriques,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }

    public function addRubrique(Request $request)
    {
        $data = new RubriqueEditFormData();
        $form = $this->formFactory->create(RubriqueType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rubrique = new Rubrique();
            $this->rubriqueEditFormDataFactory->fromRubrique($data, $rubrique);
            try {
                $this->rubriqueRepository->save($rubrique);
                $this->log('Ajout de la rubrique ' . $rubrique->getNom());
                $this->flashBag->add('notice', 'La rubrique a été ajoutée');

                return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list', ['filter' => $rubrique->getNom()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout de la rubrique');
            }
        }

        return new Response($this->render('admin/site/rubrique_add.html.twig', [
            'form' => $form->createView(),
        ]));

    }

    public function editRubrique (Request $request) 
    {     
        $champs = 'id, date, nom, etat';
        $sort = $request->query->get('sort') ? $request->query->get('sort') : 'nom';
        $direction = $request->query->get('direction') ? $request->query->get('direction') :'desc';
        $filter = $request->query->get('filter') ? $request->query->get('filter') :'desc';

        $filter='';
        $rubriques = $this->getRubriqueRepository()->getAllRubriques($champs);
        
        return new Response($this->render('admin/site/rubrique_list.html.twig', [
            'rubriques' => $rubriques,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => dump($request),
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
