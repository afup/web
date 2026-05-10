<?php

declare(strict_types=1);

namespace Afup\Site\Forum;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use CCMBenchmark\Ting\Repository\CollectionInterface;

class AppelConferencier
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function obtenirConferenciersPourSession(int $id = 0): CollectionInterface
    {
        return $this->speakerRepository->getSpeakersBySession($id);
    }

    public function obtenirListeSessionsAvecResumes(int $id_forum): array
    {
        $sessions = $this->talkRepository->getScheduledTalksByEvent($id_forum);

        $sessionsAvecId = [];
        foreach ($sessions as $session) {
            $sessionsAvecId[$session['session_id']] = $session;
        }

        $event = $this->eventRepository->get($id_forum);
        if ($event === null) {
            return [];
        }
        $directoryPath = __DIR__ . "/../../../htdocs/templates/" . $event->getPath() . "/resumes/";
        if (!is_dir($directoryPath)) {
            return [];
        }

        $sessionsAvecResumes = [];
        $repertoire = new \DirectoryIterator($directoryPath);
        foreach ($repertoire as $file) {
            if (preg_match("/^[1-9]/", $file->getFilename())) {
                $id = (int) $file->getFilename();
                if (isset($sessionsAvecId[$id])) {
                    $sessionsAvecResumes[$id] = $sessionsAvecId[$id];
                    $sessionsAvecResumes[$id]['file'] = $file->getFilename();
                }
            }
        }

        return $sessionsAvecResumes;
    }

    public function obtenirListeSessionsPlannifies(int $id_forum): array
    {
        return $this->talkRepository->getPlannedTalksWithSpeakers($id_forum);
    }

    public function obtenirListeProjets(
        int $id_forum = 0,
        string $ordre = 's.date_soumission',
        array $only_ids = [],
    ): array {
        return $this->talkRepository->getProjectTalks($id_forum, $ordre, $only_ids);
    }

}
