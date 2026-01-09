<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use Afup\Site\Comptabilite\Comptabilite;
use AppBundle\Accounting\Form\TransactionsImportType;
use AppBundle\AuditLog\Audit;
use AppBundle\Compta\Importer\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImportAction extends AbstractController
{
    public function __construct(
        private readonly Audit $audit,
        private readonly Comptabilite $compta,
        private readonly Factory $importerFactory,
        #[Autowire('%kernel.project_dir%/../tmp/')]
        private readonly string $uploadDir,
    ) {}

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(TransactionsImportType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();
            if ($uploadedFile instanceof UploadedFile) {

                $uploadedFile->move($this->uploadDir, 'banque.csv');
                $importer = $this->importerFactory->create($this->uploadDir . 'banque.csv', $form->get('bankAccount')->getData());
                if ($this->compta->extraireComptaDepuisCSVBanque($importer)) {
                    $this->audit->log('Chargement fichier banque');
                    $_SESSION['flash'] = "Le fichier a été importé";
                    $_SESSION['erreur'] = false;
                    $this->addFlash('notice', "Le fichier a été importé");
                } else {
                    $_SESSION['flash'] = "Le fichier n'a pas été importé. Le format est-il valide ?";
                    $_SESSION['erreur'] = true;
                    $this->addFlash('error', "Le fichier n'a pas été importé. Le format est-il valide ?");
                }
                unlink($this->uploadDir . 'banque.csv');
                return $this->redirect('/pages/administration/index.php?page=compta_journal&&action=lister');
            }
        }

        return $this->render('admin/accounting/journal/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
