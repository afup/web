<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use Afup\Site\Utils\PDF_AG;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Assert\Assertion;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ListingAction
{
    private GeneralMeetingRepository $generalMeetingRepository;

    public function __construct(GeneralMeetingRepository $generalMeetingRepository)
    {
        $this->generalMeetingRepository = $generalMeetingRepository;
    }

    public function __invoke(Request $request): BinaryFileResponse
    {
        $latestDate = $this->generalMeetingRepository->getLatestDate();
        Assertion::notNull($latestDate);
        $selectedDate = $latestDate;
        if ($request->query->has('date')) {
            $selectedDate = DateTimeImmutable::createFromFormat('d/m/Y', (string) $request->query->get('date'));
        }
        $attendees = $this->generalMeetingRepository->getAttendees($selectedDate);
        $filename = tempnam(sys_get_temp_dir(), 'assemblee_generale');
        $pdf = new PDF_AG();
        $pdf->setFooterTitle('Assemblée générale ' . $selectedDate->format('d/m/Y'));
        $pdf->prepareContent($attendees);
        $pdf->Output($filename, 'F', true);
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'assemblee_generale.pdf');

        return $response;
    }
}
