<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Talk;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Talk\ExportGenerator;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

readonly class ExportJoindInAction
{
    public function __construct(
        private EventRepository $eventRepository,
        private ExportGenerator $exportGenerator,
    ) {}

    public function __invoke(int $eventId): BinaryFileResponse
    {
        $event = $this->eventRepository->get($eventId);

        $file = new SplFileObject(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('talk_joind_in_', true), 'w+');
        $this->exportGenerator->exportJoindIn($event, $file);

        $response = new BinaryFileResponse($file, BinaryFileResponse::HTTP_OK, ['Content-Type' => 'text/csv; charset=utf-8']);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('talks_%s_%s_joind_in.csv', $event->getPath(), date('Ymd-His')));
        $response->deleteFileAfterSend();

        return $response;
    }
}
