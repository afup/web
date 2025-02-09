<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Twig\Environment;

class ReportsAction
{
    private Environment $twig;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private FormFactoryInterface $formFactory;

    public function __construct(Environment           $twig,
                                FlashBagInterface     $flashBag,
                                UrlGeneratorInterface $urlGenerator,
                                FormFactoryInterface  $formFactory)
    {
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request): Response
    {
        $basePath = 'uploads/general_meetings_reports/';

        // delete
        if ($fileToDelete = $request->query->get('file')) {
            $fileToDelete = $basePath . basename($fileToDelete);
            if (is_file($fileToDelete) && unlink($fileToDelete)) {
                $this->flashBag->add('notice', 'Le compte rendu a correctement été supprimé.');
            } else {
                $this->flashBag->add('error', 'Le compte rendu n\'a pas été supprimé.');
            }
            return new RedirectResponse($this->urlGenerator->generate($request->attributes->get('_route')));
        }

        // add
        $form = $this->buildForm();
        if ($form->handleRequest($request) && $form->isValid()) {
            /** @var UploadedFile $reportFile */
            $reportFile = $form->get('file')->getData();
            if ($reportFile->move($basePath, $reportFile->getClientOriginalName())) {
                $this->flashBag->add('notice', 'Le compte rendu a correctement été ajouté.');
            } else {
                $this->flashBag->add('error', 'Le compte rendu n\'a pas été ajouté.');
            }
            return new RedirectResponse($this->urlGenerator->generate($request->attributes->get('_route')));
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

        return new Response($this->twig->render('admin/members/general_meeting/reports.html.twig', [
            'form' => $form->createView(),
            'reports' => $reports
        ]));
    }

    private function humanFilesize($bytes): string
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        return sprintf("%.2f", $bytes / (1024 ** $factor)) . $sz[(int) $factor];
    }

    private function buildForm(): FormInterface
    {
        return $this->formFactory->createNamed('report')
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
            ]);
    }
}
