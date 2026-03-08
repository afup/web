<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Sheet;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\Form\SheetType;
use AppBundle\Site\Model\Repository\SheetRepository;
use AppBundle\Site\Model\Sheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddSheetAction extends AbstractController
{
    public function __construct(
        private readonly SheetRepository $sheetRepository,
        private readonly Audit $audit,
        #[Autowire('%kernel.project_dir%/../htdocs/templates/site/images')]
        private readonly string $storageDir,
    ) {}

    public function __invoke(Request $request): Response
    {
        $sheet = new Sheet();
        $form = $this->createForm(SheetType::class, $sheet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file instanceof UploadedFile) {
                $file->move($this->storageDir, $file->getClientOriginalName());
                $sheet->setImage($file->getClientOriginalName());
            }
            $this->sheetRepository->save($sheet);
            $this->audit->log('Ajout de la feuille ' . $sheet->getName());
            $this->addFlash('notice', 'La feuille ' . $sheet->getName() . ' a été ajoutée');
            return $this->redirectToRoute('admin_site_sheets_list');
        }

        return $this->render('admin/site/sheet_form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Ajouter une feuille',
            'image' => false,
            'submitLabel' => 'Ajouter',
        ]);
    }
}
