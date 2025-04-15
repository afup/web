<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Form\RubriqueType;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use AppBundle\Site\Model\Rubrique;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddRubriqueAction extends AbstractController
{
    use DbLoggerTrait;

    private RubriqueRepository $rubriqueRepository;
    private string $storageDir;

    public function __construct(
        RubriqueRepository $rubriqueRepository,
        string $storageDir
    ) {
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->storageDir = $storageDir;
    }

    public function __invoke(Request $request): Response
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
            $this->addFlash('notice', 'La rubrique ' . $rubrique->getNom() . ' a été ajoutée');
            return $this->redirectToRoute('admin_site_rubriques_list', [
                'filter' => $rubrique->getNom()
            ]);
        }

        return $this->render('admin/site/rubrique_form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Ajouter une rubrique',
            'icone' => false,
            'submitLabel' => 'Ajouter',
        ]);
    }
}
