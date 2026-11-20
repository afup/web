<?php

declare(strict_types=1);

namespace AppBundle\CFP;

use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
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

        $speaker = $this->speakerRepository->getOneBy([
            'user' => $user,
            'eventId' => $event->getId(),
        ]);

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
            $adminSpeaker = $adminSpeakerId !== null ? $this->speakerRepository->get($adminSpeakerId) : null;
            if ($adminSpeaker instanceof Speaker) {
                $adminSpeaker->setUser($user);
                $this->speakerRepository->save($adminSpeaker);

                return $adminSpeaker;
            }
        }

        $speaker = new Speaker();
        $speaker
            ->setUser($user)
            ->setEventId($event->getId())
            ->setPersonneId($personneId)
        ;

        return $speaker;
    }
}
