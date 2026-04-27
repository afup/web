<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\Entity\Repository\RubriqueRepository;
use AppBundle\Site\Form\RubriqueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditRubriqueAction extends AbstractController
{
    public function __construct(
        private readonly RubriqueRepository $rubriqueRepository,
        #[Autowire('%kernel.project_dir%/../htdocs/templates/site/images')]
        private readonly string $storageDir,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $rubrique = $this->rubriqueRepository->find($id);
        $form = $this->createForm(RubriqueType::class, $rubrique);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('icone')->getData();
            if ($file) {
                $originalFilename = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = hash('sha1', $originalFilename);
                $newFilename = $safeFilename . '.' . $file->guessExtension();
                $file->move($this->storageDir, $newFilename);
                $rubrique->icone = $newFilename;
            }
            $this->rubriqueRepository->save($rubrique);
            $this->audit->log('Modification de la Rubrique ' . $rubrique->nom);
            $this->addFlash('notice', 'La rubrique ' . $rubrique->nom . ' a été modifiée');
            return $this->redirectToRoute('admin_site_rubriques_list', [
                'filter' => $rubrique->nom,
            ]);
        }
        $icone = $rubrique->icone !== null ? '/templates/site/images/' . $rubrique->icone : false;

        return $this->render('admin/site/rubrique_form.html.twig', [
            'form' => $form->createView(),
            'icone' => $icone,
            'formTitle' => 'Modifier une rubrique',
            'submitLabel' => 'Modifier',
        ]);
    }
}
