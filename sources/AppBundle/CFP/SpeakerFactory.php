<?php

declare(strict_types=1);

namespace AppBundle\CFP;

use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Model\Event;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SpeakerFactory
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly SpeakerRepository $speakerRepository,
        private readonly PersonneConferencierRepository $personneConferencierRepository,
    ) {}

    /**
     * Get a speaker from current logged in user or create a new speaker if profile has not been created
     */
    public function getSpeaker(Event $event): Speaker
    {
        // Try to get a speaker for the current logged in user
        $user = null;
        if ($this->tokenStorage->getToken() instanceof TokenInterface) {
            $user = $this->tokenStorage->getToken()->getUser();
            if ($user instanceof GithubUser) {
                $user = $user->getId();
            }
        }

        $speaker = is_int($user) ? $this->speakerRepository->findOneBy([
            'githubUserId' => $user,
            'eventId' => $event->getId(),
        ]) : null;

        if ($speaker !== null) {
            return $speaker;
        }

        $personneId = null;
        if (is_int($user)) {
            $personneId = $this->personneConferencierRepository->findLatestIdByGithubUser($user);
            // La personne a peut-être déjà une ligne pour cet événement, saisie par l'admin sans compte GitHub :
            // on la reprend plutôt que de créer un doublon.
            $adminSpeakerId = $personneId !== null && $event->getId() !== null
                ? $this->personneConferencierRepository->findSpeakerIdInEvent($personneId, $event->getId(), withoutGithubUser: true)
                : null;
            $adminSpeaker = $adminSpeakerId !== null ? $this->speakerRepository->find($adminSpeakerId) : null;
            if ($adminSpeaker instanceof Speaker) {
                $adminSpeaker->githubUserId = $user;
                $this->speakerRepository->save($adminSpeaker);

                return $adminSpeaker;
            }
        }

        $speaker = new Speaker();
        $speaker->githubUserId = is_int($user) ? $user : null;
        $speaker->eventId = (int) $event->getId();
        $speaker->personneId = $personneId;

        return $speaker;
    }
}
