<?php

declare(strict_types=1);


namespace AppBundle\CFP;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SpeakerFactory
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly SpeakerRepository $speakerRepository,
    ) {
    }

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

        if ($speaker === null) {
            $speaker = new Speaker();
            $speaker
                ->setUser($user)
                ->setEventId($event->getId())
            ;
        }

        return $speaker;
    }
}
