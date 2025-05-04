<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PreviousRegistrationsAction
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    public function __invoke(Request $request): BinaryFileResponse
    {
        $file = new SplFileObject(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('inscrits_', true), 'w+');
        $events = $this->eventRepository->getPreviousEvents($request->query->getInt('event_count', 4));
        foreach ($this->ticketRepository->getRegistrationsForEventsWithNewsletterAllowed($events) as $registration) {
            $file->fputcsv($registration);
        }

        $response = new BinaryFileResponse($file, BinaryFileResponse::HTTP_OK, ['Content-Type' => 'text/csv; charset=utf-8']);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('inscriptions_%d_derniers_events.csv', date('Ymd-His')));
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
