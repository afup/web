<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\EventRepository;
use Assert\Assertion;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final readonly class EventActionHelper
{
    public function __construct(
        private EventRepository $eventRepository,
        private TokenStorageInterface $tokenStorage,
    ) {}

    /**
     * @throws NotFoundHttpException
     */
    public function getEvent(string $eventSlug): Event
    {
        $event = $this->eventRepository->getOneBy(['path' => $eventSlug]);
        if ($event === null) {
            throw new NotFoundHttpException('Event not found');
        }

        return $event;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function getEventById(int|string|null $id = null, bool $allowFallback = true): Event
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

    public function getGithubUser(): GithubUser
    {
        $token = $this->tokenStorage->getToken();
        Assertion::notNull($token);

        $user = $token->getUser();
        Assertion::isInstanceOf($user, GithubUser::class);

        return $user;
    }
}
