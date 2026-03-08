<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Model\Transaction;
use AppBundle\Accounting\Model\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DownloadAttachmentAction extends AbstractController
{
    public function __construct(
        private readonly TransactionRepository $accountingRepository,
        #[Autowire('%kernel.project_dir%/../htdocs/uploads/')]
        private readonly string $uploadDir,
    ) {}

    public function __invoke(Request $request, int $id): Response
    {
        $accounting = $this->accountingRepository->get($id);
        if (!$accounting instanceof Transaction) {
            throw $this->createNotFoundException();
        }

        $path = $this->uploadDir . $accounting->getAttachmentFilename();
        if ($accounting->getAttachmentFilename() === null || !is_file($path)) {
            throw $this->createNotFoundException('No attachment found');
        }

        return new BinaryFileResponse($path, Response::HTTP_OK, [
            'Content-disposition' => 'attachment; filename="' . basename($path) . '"',
        ], false);
    }
}
