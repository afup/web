<?php

declare(strict_types=1);

namespace AppBundle\CFP;

use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Model\Event;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SpeakerFactory
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly SpeakerRepository $speakerRepository,
        private readonly PersonneConferencierRepository $personneConferencierRepository,
    ) {}

    /**
     * Profil conférencier enregistré de l'utilisateur GitHub connecté pour l'événement, null s'il n'en a pas encore.
     */
    public function findSpeaker(Event $event): ?Speaker
    {
        $githubUserId = $this->getGithubUserId();
        if ($githubUserId === null) {
            return null;
        }

        $speaker = $this->speakerRepository->findOneBy([
            'githubUserId' => $githubUserId,
            'eventId' => $event->getId(),
        ]);

        if ($speaker !== null) {
            return $speaker;
        }

        // La personne a peut-être déjà une ligne pour cet événement, saisie par l'admin sans compte GitHub :
        // on la reprend plutôt que de créer un doublon.
        $personneId = $this->personneConferencierRepository->findLatestIdByGithubUser($githubUserId);
        $adminSpeakerId = $personneId !== null && $event->getId() !== null
            ? $this->personneConferencierRepository->findSpeakerIdInEvent($personneId, $event->getId(), withoutGithubUser: true)
            : null;
        $adminSpeaker = $adminSpeakerId !== null ? $this->speakerRepository->find($adminSpeakerId) : null;
        if (!$adminSpeaker instanceof Speaker) {
            return null;
        }

        $adminSpeaker->githubUserId = $githubUserId;
        $this->speakerRepository->save($adminSpeaker);

        return $adminSpeaker;
    }

    /**
     * Nouveau profil conférencier, non enregistré, pour l'utilisateur GitHub connecté.
     */
    public function createSpeaker(Event $event): Speaker
    {
        $githubUserId = $this->getGithubUserId();

        $speaker = new Speaker();
        $speaker->githubUserId = $githubUserId;
        $speaker->eventId = (int) $event->getId();
        $speaker->personneId = $githubUserId !== null
            ? $this->personneConferencierRepository->findLatestIdByGithubUser($githubUserId)
            : null;

        return $speaker;
    }

    private function getGithubUserId(): ?int
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        return $user instanceof GithubUser ? $user->getId() : null;
    }
}
