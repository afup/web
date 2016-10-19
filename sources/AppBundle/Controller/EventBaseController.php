<?php


namespace AppBundle\Controller;


use AppBundle\Model\Event;
use AppBundle\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class EventBaseController extends Controller
{
    /**
     * @param $eventSlug
     * @return Event|null
     *
     * @throws NotFoundHttpException
     */
    protected function checkEventSlug($eventSlug)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $eventRepository->getOneBy(['path' => $eventSlug]);

        if ($event === null) {
            throw $this->createNotFoundException('Event not found');
        }

        return $event;
    }
}
