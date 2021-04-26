<?php

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Form\RubriqueEditFormData;
use AppBundle\Site\Form\RubriqueFormDataFactory;
use AppBundle\Site\Form\RubriqueType;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

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

    /** @var string */
    private $storageDir;

    public function __construct(
        FormFactoryInterface $formFactory,
        RubriqueFormDataFactory  $rubriqueFormDataFactory,
        RubriqueRepository $rubriqueRepository,
        RubriqueEditFormData $rubriqueEditFormData,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        $storageDir
    ) {
        $this->formFactory = $formFactory;
        $this->rubriqueFormDataFactory = $rubriqueFormDataFactory;
        $this->rubriqueEditFormData = $rubriqueEditFormData;
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->storageDir = $storageDir;
    }


    /**
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke($id,Request $request)
    {
        $rubrique = $this->rubriqueRepository->get($id);

        $old = $this->rubriqueFormDataFactory->fromRubrique($rubrique);

        $form = $this->formFactory->create(RubriqueType::class, $old);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

             /* Handling the icon file : */
            $file = $form->get('icone')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = hash('sha256', $originalFilename);
                $newFilename = $safeFilename . '.' . $file->guessExtension();

                try {
                    $file->move($this->storageDir, $newFilename);
                    $rubrique->setIcone($newFilename);
                } catch (FileException $e) {
                    $this->flashBag->add('error', 'Une erreur est survenue lors du traitement de l\'icône');
                }
            }

            $this->rubriqueFormDataFactory->toRubrique($old, $rubrique);
            try {
                $this->rubriqueRepository->save($rubrique);
                $this->log('Modification de la Rubrique ' . $rubrique->getNom());
                $this->flashBag->add('notice', 'La rubrique a été modifiée');
                return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list', ['filter' => $rubrique->getNom()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de la modification de la rubrique');
            }
        }
        $icone = $rubrique->getIcone() !== null ? $this->storageDir . DIRECTORY_SEPARATOR . $rubrique->getIcone() : false;
        return new Response($this->twig->render('admin/site/rubrique_form.html.twig', [
            'form' => $form->createView(),
            'icone' => $icone,
            'formTitle' => 'Modifier une rubrique',
        ]));
    }
}
