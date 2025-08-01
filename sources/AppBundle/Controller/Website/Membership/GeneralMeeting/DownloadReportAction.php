<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\GeneralMeeting;

use AppBundle\Association\Model\User;
use AppBundle\GeneralMeeting\ReportListBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class DownloadReportAction extends AbstractController
{
    public function __construct(
        private readonly ReportListBuilder $reportListBuilder,
    ) {}

    public function __invoke($filename): BinaryFileResponse
    {
        $reports = $this->reportListBuilder->prepareGeneralMeetingsReportsList();

        if (!isset($reports[$filename])) {
            throw $this->createNotFoundException();
        }

        if ($this->getUser() instanceof User
            && $this->getUser()->hasRole('ROLE_MEMBER_EXPIRED')) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($reports[$filename]['path']);
    }
}
