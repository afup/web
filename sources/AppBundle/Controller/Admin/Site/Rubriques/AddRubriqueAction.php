<?php

namespace AppBundle\Controller\Admin\Site\Rubriques;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Controller\SiteBaseController;
use AppBundle\Site\Form\RubriqueType;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use AppBundle\Site\Model\Rubrique;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class AddRubriqueAction extends SiteBaseController
{
    use DbLoggerTrait;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Environment */
    private $twig;

    /** @var RubriqueRepository */
    private $rubriqueRepository;

    /** @var string */
    private $storageDir;

    public function __construct(
        RubriqueRepository $rubriqueRepository,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        $storageDir
    ) {
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->storageDir = $storageDir;
    }

    public function __invoke(Request $request)
    {
        $rubrique = new Rubrique();
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
            $this->log('Ajout de la rubrique ' . $rubrique->getNom());
            $this->flashBag->add('notice', 'La rubrique ' . $rubrique->getNom() . ' a été ajoutée');
            return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list', ['filter' => $rubrique->getNom()]));
        }

        return new Response($this->twig->render('admin/site/rubrique_form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Ajouter une rubrique',
            'icone' => false,
            'submitLabel' => 'Ajouter',
        ]));
    }
}
