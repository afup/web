<?php

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Model\Rubrique;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use AppBundle\Site\Form\RubriqueEditFormData;
use AppBundle\Site\Form\RubriqueFormDataFactory;
use AppBundle\Site\Form\RubriqueType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Assert\Assertion;
use Exception;

class EditRubriqueAction
{
    use DbLoggerTrait;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Environment */
    private $twig;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RubriqueFormDataFactory */
    private $rubriqueFormDataFactory;

    /** @var RubriqueEditFormData */
    private $rubriqueEditFormData;

    /** @var RubriqueRepository */
    private $rubriqueRepository;
    
    public function __construct(
        FormFactoryInterface $formFactory, 
        RubriqueFormDataFactory  $rubriqueFormDataFactory,  
        RubriqueRepository $rubriqueRepository,
        RubriqueEditFormData $rubriqueEditFormData,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    )
    {
        $this->formFactory = $formFactory;
        $this->rubriqueFormDataFactory = $rubriqueFormDataFactory;
        $this->rubriqueEditFormData = $rubriqueEditFormData;
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }
  
    public function __invoke (Request $request) 
    {   
        $data = $this->rubriqueRepository->getOneById($GLOBALS['AFUP_DB']->echapper($request->get('id')));
        $rubrique = new Rubrique();
        $rubrique->setNom($data["nom"]);
        $rubrique->setIdPersonnePhysique($data["id_personne_physique"]);
        $rubrique->setIcone($data["icone"]);
        $rubrique->setId($data["id"]);
        $rubrique->setRaccourci($data["raccourci"]);
        $rubrique->setContenu($data["contenu"]);
        $rubrique->setDescriptif($data["descriptif"]);
        $rubrique->setPosition($data["position"]);
        $rubrique->setDate($data["date"]);
        $rubrique->setEtat($data["etat"]);
        $rubrique->setPagination($data["pagination"]);
        $rubrique->setFeuilleAssociee($data["feuille_associee"]);
        $rubrique->setIdParent($data["id_parent"]);

        $old = $this->rubriqueFormDataFactory->fromRubrique($rubrique);

        $form = $this->formFactory->create(RubriqueType::class, $old);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

             /* Handling the icon file : */
             $file = $form->get('icone')->getData();
             if ($file) {
                 $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                 $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                 $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
 
                 try {
                     $file->move(dirname(__FILE__).'/../../templates/site/images/', $newFilename);
                 } catch (FileException $e) {
                     $this->flashBag->add('error', 'Une erreur est survenue lors du traitement de l\'icône');
                 }
                 $rubrique->setIcone($newFilename);
             }

            $this->rubriqueFormDataFactory->toRubrique($old, $rubrique);
            try {
                $this->rubriqueRepository->updateRubrique($rubrique);
                $this->log('Modification de la Rubrique ' . $rubrique->getNom());
                $this->flashBag->add('notice', 'La rubrique a été modifiée');
                return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list', ['filter' => $rubrique->getNom()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de la modification de la rubrique');
            }
        }
        $icone = $rubrique->getIcone() !== null ? $GLOBALS['AFUP_CONF']->obtenir('web|path').'templates/site/images/'.$rubrique->getIcone() : false;
        return new Response($this->twig->render('admin/site/rubrique_edit.html.twig', [
            'form' => $form->createView(),
            'icone' => $icone
        ]));
    } 
    
}
