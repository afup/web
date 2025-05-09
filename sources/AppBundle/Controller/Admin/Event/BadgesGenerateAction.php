<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Ticket\RegistrationsExportGenerator;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BadgesGenerateAction
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly RegistrationsExportGenerator $registrationsExportGenerator,
    ) {
    }

    public function __invoke(Request $request): BinaryFileResponse
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'), false);
        $file = new SplFileObject(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('badges_', true), 'w+');
        $this->registrationsExportGenerator->export($event, $file);
        $response = new BinaryFileResponse($file, BinaryFileResponse::HTTP_OK, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('inscriptions_%s_%s.csv', $event->getPath(), date('Ymd-His')));
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
