<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Form\RubriqueType;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EditRubriqueAction extends AbstractController
{
    use DbLoggerTrait;

    private FlashBagInterface $flashBag;

    private UrlGeneratorInterface $urlGenerator;

    private Environment $twig;

    private RubriqueRepository $rubriqueRepository;

    /** @var string */
    private $storageDir;

    public function __construct(RubriqueRepository $rubriqueRepository,Environment $twig,UrlGeneratorInterface $urlGenerator,FlashBagInterface $flashBag,$storageDir)
    {
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->storageDir = $storageDir;
    }

    /**
     * @param int $id
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke($id,Request $request)
    {
        $rubrique = $this->rubriqueRepository->get($id);
        $form = $this->createForm(RubriqueType::class, $rubrique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('icone')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = hash('sha1', $originalFilename);
                $newFilename = $safeFilename . '.' . $file->guessExtension();
                $file->move($this->storageDir, $newFilename);
                $rubrique->setIcone($newFilename);
            }
            $this->rubriqueRepository->save($rubrique);
            $this->log('Modification de la Rubrique ' . $rubrique->getNom());
            $this->flashBag->add('notice', 'La rubrique ' . $rubrique->getNom() . ' a été modifiée');
            return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list', ['filter' => $rubrique->getNom()]));
        }
        $icone = $rubrique->getIcone() !== null ? '/templates/site/images/' . $rubrique->getIcone() : false;
        return new Response($this->twig->render('admin/site/rubrique_form.html.twig', [
            'form' => $form->createView(),
            'icone' => $icone,
            'formTitle' => 'Modifier une rubrique',
            'submitLabel' => 'Modifier',
        ]));
    }
}
