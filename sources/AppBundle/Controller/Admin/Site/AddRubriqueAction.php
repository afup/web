<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\Form\RubriqueType;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use AppBundle\Site\Model\Rubrique;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddRubriqueAction extends AbstractController
{
    public function __construct(
        private readonly RubriqueRepository $rubriqueRepository,
        #[Autowire('%kernel.project_dir%/../htdocs/templates/site/images')]
        private readonly string $storageDir,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $rubrique = new Rubrique();
        $form = $this->createForm(RubriqueType::class, $rubrique);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('icone')->getData();
            if ($file) {
                $originalFilename = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = hash('sha1', $originalFilename);
                $newFilename = $safeFilename . '.' . $file->guessExtension();
                $file->move($this->storageDir, $newFilename);
                $rubrique->setIcone($newFilename);
            }
            $this->rubriqueRepository->save($rubrique);
            $this->audit->log('Ajout de la rubrique ' . $rubrique->getNom());
            $this->addFlash('notice', 'La rubrique ' . $rubrique->getNom() . ' a été ajoutée');
            return $this->redirectToRoute('admin_site_rubriques_list', [
                'filter' => $rubrique->getNom(),
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
