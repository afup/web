<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Feuille;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use AppBundle\Site\Form\FeuilleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditFeuilleAction extends AbstractController
{
    public function __construct(
        private readonly FeuilleRepository $feuilleRepository,
        private readonly Audit $audit,
        #[Autowire('%kernel.project_dir%/../htdocs/templates/site/images')]
        private readonly string $storageDir,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $feuille = $this->feuilleRepository->find($id);
        $form = $this->createForm(FeuilleType::class, $feuille);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file instanceof UploadedFile) {
                $file->move($this->storageDir, $file->getClientOriginalName());
                $feuille->image = $file->getClientOriginalName();
            }
            $this->feuilleRepository->save($feuille);
            $this->audit->log('Modification de la feuille ' . $feuille->nom);
            $this->addFlash('notice', 'La feuille ' . $feuille->nom . ' a été modifiée');
            return $this->redirectToRoute('admin_site_feuilles_list');
        }
        $image = $feuille->image !== null ? '/templates/site/images/' . $feuille->image : false;

        return $this->render('admin/site/feuille_form.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
            'formTitle' => 'Modifier une feuille',
            'submitLabel' => 'Modifier',
        ]);
    }
}
