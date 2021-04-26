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
use Exception;

class AddRubriqueAction
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

    /** @var RubriqueRepository */
    private $rubriqueRepository;
    
    public function __construct(
        FormFactoryInterface $formFactory, 
        RubriqueFormDataFactory  $rubriqueFormDataFactory,  
        RubriqueRepository $rubriqueRepository,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    )
    {
        $this->formFactory = $formFactory;
        $this->rubriqueFormDataFactory = $rubriqueFormDataFactory;
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request)
    {
        $data = new RubriqueEditFormData();
        $form = $this->formFactory->create(RubriqueType::class, $data);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $rubrique = new Rubrique();

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
            $this->rubriqueFormDataFactory->toRubrique($form->getData(),$rubrique);

            try {
                $this->rubriqueRepository->insertRubrique($rubrique);
                $this->log('Ajout de la rubrique ' . $rubrique->getNom());
                $this->flashBag->add('notice', 'La rubrique a été ajoutée');
                return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list', ['filter' => $rubrique->getNom()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout de la rubrique');
            }
        }

        return new Response($this->twig->render('admin/site/rubrique_form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Modifier une rubrique',
        ]));
    }

    
    
}
