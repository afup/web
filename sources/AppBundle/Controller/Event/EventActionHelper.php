<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\EventRepository;
use Assert\Assertion;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EventActionHelper
{
    public function __construct(
        protected EventRepository $eventRepository,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * @param string $eventSlug
     *
     * @return Event
     *
     * @throws NotFoundHttpException
     */
    public function getEvent($eventSlug)
    {
        $event = $this->eventRepository->getOneBy(['path' => $eventSlug]);
        if ($event === null) {
            throw new NotFoundHttpException('Event not found');
        }

        return $event;
    }

    /**
     * @param int|null $id
     * @param bool     $allowFallback
     *
     * @return Event
     */
    public function getEventById($id = null, $allowFallback = true)
    {
        $event = null;
        if (null !== $id) {
            $event = $this->eventRepository->get((int) $id);
        } elseif ($allowFallback) {
            $event = $this->eventRepository->getNextEvent();

            if (null === $event && null !== ($latestEvent = $this->eventRepository->getLastEvent())) {
                $event = $latestEvent;
            }
        }

        if ($event === null) {
            throw new NotFoundHttpException('Could not find event');
        }

        return $event;
    }

    /**
     * @return GithubUser
     */
    public function getUser()
    {
        $token = $this->tokenStorage->getToken();
        Assertion::notNull($token);
        /** @var GithubUser $user */
        $user = $token->getUser();
        Assertion::isInstanceOf($user, GithubUser::class);

        return $user;
    }
}
