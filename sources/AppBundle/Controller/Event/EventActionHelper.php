<?php

namespace AppBundle\Controller\Event;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\EventRepository;
use Assert\Assertion;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig_Environment;

class EventActionHelper
{
    /** @var EventRepository */
    protected $eventRepository;
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var Twig_Environment */
    private $twig;

    public function __construct(
        EventRepository $eventRepository,
        TokenStorageInterface $tokenStorage,
        Twig_Environment $twig
    ) {
        $this->eventRepository = $eventRepository;
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
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
