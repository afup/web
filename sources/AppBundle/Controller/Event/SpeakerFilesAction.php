<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpeakerFilesAction
{
    private SpeakersExpensesStorage $speakersExpensesStorage;
    private SpeakerRepository $speakerRepository;
    private EventActionHelper $eventActionHelper;

    public function __construct(
        SpeakersExpensesStorage $speakersExpensesStorage,
        SpeakerRepository $speakerRepository,
        EventActionHelper $eventActionHelper
    ) {
        $this->speakersExpensesStorage = $speakersExpensesStorage;
        $this->speakerRepository = $speakerRepository;
        $this->eventActionHelper = $eventActionHelper;
    }

    public function __invoke(Request $request): BinaryFileResponse
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        $speaker = $this->speakerRepository->get($request->get('speakerId'));
        if ($speaker->getEventId() !== $event->getId()) {
            throw new NotFoundHttpException(sprintf('Event id (%d) not found', $event->getId()));
        }
        $files = $this->speakersExpensesStorage->getFiles($speaker);
        foreach ($files as $file) {
            if ($file['basename'] === $request->attributes->get('filename')) {
                return new BinaryFileResponse($file['path']);
            }
        }

        throw new NotFoundHttpException(sprintf('File (%s) not found', $request->attributes->get('filename')));
    }
}
