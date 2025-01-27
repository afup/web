<?php


namespace AppBundle\Controller\Event;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class EventBaseController extends Controller
{
    /**
     * @param $eventSlug
     * @return Event
     *
     * @throws NotFoundHttpException
     */
    protected function checkEventSlug($eventSlug)
    {
        /**
         * @var EventRepository $eventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $eventRepository->getOneBy(['path' => $eventSlug]);

        if ($event === null) {
            throw $this->createNotFoundException('Event not found');
        }

        return $event;
    }
}
