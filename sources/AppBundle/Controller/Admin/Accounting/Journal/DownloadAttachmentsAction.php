<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Journal;

use AppBundle\Accounting\Model\Repository\InvoicingPeriodRepository;
use DateInterval;
use DatePeriod;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class DownloadAttachmentsAction extends AbstractController
{
    public function __construct(
        private readonly InvoicingPeriodRepository $invoicingPeriodRepository,
        #[Autowire('%kernel.project_dir%/../htdocs/uploads/')] private readonly string $uploadDir,
    ) {}

    public function __invoke(Request $request): Response
    {
        $periodId = $request->query->has('periodId') && $request->query->get('periodId') ? (int) $request->query->get('periodId') : null;
        $period = $this->invoicingPeriodRepository->getCurrentPeriod($periodId);
        $year = $period->getStartDate()->format('Y');

        // Create the zip
        $zipFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'afup_justificatifs-' . $year . '.zip';
        $zip = new ZipArchive();
        $state = $zip->open($zipFilename, ZipArchive::CREATE);
        if ($state !== true) {
            throw new RuntimeException("Impossible to open the Zip archive.");
        }

        $datePeriod = new DatePeriod($period->getStartDate(), new DateInterval('P1M'), $period->getEndDate());
        /** @var \DateTime $month */
        foreach ($datePeriod as $month) {
            $directory = $year . $month->format('m');
            $options = [
                'add_path' => 'afup_justificatifs-' . $year . '/' . $directory . '/',
                'remove_all_path' => true,
            ];
            $zip->addGlob($this->uploadDir . $directory . '/*.*', 0, $options);
        }
        $zip->close();

        $response = new BinaryFileResponse($zipFilename, Response::HTTP_OK, [
            'Content-Type' => 'application/zip',
            'Content-Transfer-Encoding' => 'Binary',
            'Content-Disposition' => 'attachment; filename="' . basename($zipFilename) . '"',
            'Cache-Control' => 'max-age=0',
        ], false);
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
