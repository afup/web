<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Speaker\ExportGenerator;
use Assert\Assertion;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportAction
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly ExportGenerator $exportGenerator,
    ) {
    }

    public function __invoke(Request $request): BinaryFileResponse
    {
        if ($request->query->has('id')) {
            $event = $this->eventRepository->get($request->query->getInt('id'));
        } else {
            $event = $this->eventRepository->getNextEvent();
        }
        Assertion::notNull($event);
        $file = new SplFileObject(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('speaker_', true), 'w+');
        $this->exportGenerator->export($event, $file);
        $response = new BinaryFileResponse($file, BinaryFileResponse::HTTP_OK, ['Content-Type' => 'text/html; charset=utf-8']);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('speakers_%s_%s.csv', $event->getPath(), date('Ymd-His')));
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
