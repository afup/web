<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Form\RubriqueType;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditRubriqueAction extends AbstractController
{
    use DbLoggerTrait;

    private RubriqueRepository $rubriqueRepository;

    private string $storageDir;

    public function __construct(RubriqueRepository $rubriqueRepository, string $storageDir)
    {
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->storageDir = $storageDir;
    }

    public function __invoke(int $id,Request $request): Response
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
            $this->addFlash('notice', 'La rubrique ' . $rubrique->getNom() . ' a été modifiée');
            return $this->redirectToRoute('admin_site_rubriques_list', [
                'filter' => $rubrique->getNom()
            ]);
        }
        $icone = $rubrique->getIcone() !== null ? '/templates/site/images/' . $rubrique->getIcone() : false;

        return $this->render('admin/site/rubrique_form.html.twig', [
            'form' => $form->createView(),
            'icone' => $icone,
            'formTitle' => 'Modifier une rubrique',
            'submitLabel' => 'Modifier',
        ]);
    }
}
