<?php

namespace AppBundle\Controller\Event;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractEventAction
{
    /** @var EventRepository */
    protected $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param string $eventSlug
     *
     * @return Event
     *
     * @throws NotFoundHttpException
     */
    protected function checkEventSlug($eventSlug)
    {
        $event = $this->eventRepository->getOneBy(['path' => $eventSlug]);

        if ($event === null) {
            throw new NotFoundHttpException('Event not found');
        }

        return $event;
    }
}
