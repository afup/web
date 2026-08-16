<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Entity\Repository\TransactionRepository;
use AppBundle\Accounting\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadAttachmentAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly ValidatorInterface $validator,
        #[Autowire('%kernel.project_dir%/htdocs/uploads/')] private readonly string $uploadDir,
    ) {}

    public function __invoke(Request $request, int $id): Response
    {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction instanceof Transaction) {
            throw $this->createNotFoundException();
        }

        if (!$request->files->get('file') instanceof UploadedFile) {
            return new Response('No file uploaded', 400);
        }

        $directory = $transaction->dateEcriture->format('Ym') . DIRECTORY_SEPARATOR;
        $targetDir = $this->uploadDir . $directory;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0750, true);
        }
        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        $violations = $this->validator->validate($file, [
            new File(mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'], maxSize: '1M'),
        ]);
        foreach ($violations as $violation) {
            return new Response($violation->getMessage(), 400);
        }

        $filename = sprintf('%s.%s',
            $transaction->dateEcriture->format('Y-m-d') . '_' . $transaction->id . '_' . substr(sha1_file($file->getPathname()), 0, 6),
            $file->guessExtension(),
        );
        $file->move($targetDir, $filename);

        if (!empty($transaction->nomJustificatif)) {
            $oldFilename = $this->uploadDir . $transaction->nomJustificatif;
            if (is_file($oldFilename)) {
                unlink($oldFilename);
            }
        }

        $transaction->nomJustificatif = $directory . $filename;
        $this->transactionRepository->save($transaction);

        return new Response();
    }
}
