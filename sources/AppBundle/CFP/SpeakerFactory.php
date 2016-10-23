<?php


namespace AppBundle\CFP;


use AppBundle\Model\Event;
use AppBundle\Model\Repository\SpeakerRepository;
use AppBundle\Model\Speaker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SpeakerFactory
{
    private $tokenStorage;
    private $speakerRepository;

    public function __construct(TokenStorage $tokenStorage, SpeakerRepository $speakerRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->speakerRepository = $speakerRepository;
    }

    /**
     * Get a speaker from current logged in user or create a new speaker if profile has not been created
     *
     * @param Event $event
     * @return Speaker
     */
    public function getSpeaker(Event $event)
    {
        // Try to get a speaker for the current logged in user
        $speaker = $this->speakerRepository->getOneBy(['user' => $this->tokenStorage->getToken()->getUser()->getId(), 'eventId' => $event->getId()]);

        if ($speaker === null) {
            $speaker = new Speaker();
            $speaker
                ->setUser($this->tokenStorage->getToken()->getUser()->getId())
                ->setEventId($event->getId())
            ;
        }

        return $speaker;
    }
}
