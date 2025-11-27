<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\GeneralMeeting;

use AppBundle\GeneralMeeting\ReportListBuilder;
use AppBundle\Security\Authentication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class DownloadReportAction extends AbstractController
{
    public function __construct(
        private readonly ReportListBuilder $reportListBuilder,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke($filename): BinaryFileResponse
    {
        $reports = $this->reportListBuilder->prepareGeneralMeetingsReportsList();

        if (!isset($reports[$filename])) {
            throw $this->createNotFoundException();
        }

        if ($this->authentication->getAfupUser()->hasRole('ROLE_MEMBER_EXPIRED')) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($reports[$filename]['path']);
    }
}
