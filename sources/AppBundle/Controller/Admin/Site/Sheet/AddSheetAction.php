<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Sheet;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Form\SheetType;
use AppBundle\Site\Model\Repository\SheetRepository;
use AppBundle\Site\Model\Sheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddSheetAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly SheetRepository $sheetRepository,
        #[Autowire('%kernel.project_dir%/../htdocs/templates/site/images')]
        private string $storageDir,
    ) {}

    public function __invoke(Request $request): Response
    {
        $sheet = new Sheet();
        $form = $this->createForm(SheetType::class, $sheet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $originalFilename = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = hash('sha1', $originalFilename);
                $newFilename = $safeFilename . '.' . $file->guessExtension();
                $file->move($this->storageDir, $newFilename);
                $sheet->setImage($newFilename);
            }
            $this->sheetRepository->save($sheet);
            $this->log('Ajout de la feuille ' . $sheet->getName());
            $this->addFlash('notice', 'La feuille ' . $sheet->getName() . ' a été ajoutée');
            return $this->redirectToRoute('admin_site_sheets_list', [
                'filter' => $sheet->getName(),
            ]);
        }

        return $this->render('admin/site/sheet_form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Ajouter une feuille',
            'image' => false,
            'submitLabel' => 'Ajouter',
        ]);
    }
}
