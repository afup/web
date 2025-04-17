<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReportsAction extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        $basePath = 'uploads/general_meetings_reports/';

        // delete
        if ($fileToDelete = $request->query->get('file')) {
            $fileToDelete = $basePath . basename($fileToDelete);
            if (is_file($fileToDelete) && unlink($fileToDelete)) {
                $this->addFlash('notice', 'Le compte rendu a correctement été supprimé.');
            } else {
                $this->addFlash('error', 'Le compte rendu n\'a pas été supprimé.');
            }
            return $this->redirectToRoute($request->attributes->get('_route'));
        }

        // add
        $form = $this->buildForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $reportFile */
            $reportFile = $form->get('file')->getData();
            if ($reportFile->move($basePath, $reportFile->getClientOriginalName())) {
                $this->addFlash('notice', 'Le compte rendu a correctement été ajouté.');
            } else {
                $this->addFlash('error', 'Le compte rendu n\'a pas été ajouté.');
            }
            return $this->redirectToRoute($request->attributes->get('_route'));
        }

        $files = glob($basePath . '*.pdf');
        rsort($files);

        $reports = [];
        foreach ($files as $file) {
            $report = pathinfo($file);

            $report['size'] = $this->humanFilesize(filesize($file));
            $report['mtime'] = filemtime($file);
            $reports[] = $report;
        }

        return $this->render('admin/members/general_meeting/reports.html.twig', [
            'form' => $form->createView(),
            'reports' => $reports
        ]);
    }

    private function humanFilesize($bytes): string
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        return sprintf("%.2f", $bytes / (1024 ** $factor)) . $sz[(int) $factor];
    }

    private function buildForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'Fichier',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Uniquement des fichiers PDF.',
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
            ])->getForm();
    }
}
