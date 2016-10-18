<?php


namespace AppBundle\Controller;


use AppBundle\Form\SpeakerType;
use AppBundle\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    public function indexAction()
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $eventRepository->getNextEvent();

        if ($event === null) {
            return $this->render(':event:none.html.twig');
        }
        return new RedirectResponse($this->generateUrl('event', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);

    }

    public function eventAction($eventSlug)
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $eventRepository->getOneBy(['path' => $eventSlug]);

        if ($event === null) {
            throw $this->createNotFoundException('Event not found');
        }
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event/cfp:closed.html.twig', ['event' => $event]);
        }
        return $this->render(':event/cfp:home.html.twig', ['event' => $event]);
    }

    public function cfpAction($eventSlug)
    {

        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $eventRepository->getOneBy(['path' => $eventSlug]);

        if ($event === null) {
            throw $this->createNotFoundException('Event not found');
        }
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event/cfp:closed.html.twig', ['event' => $event]);
        }

        $speaker = new \AppBundle\Model\Speaker(); // @todo get it from session

        $form = $this->createForm(SpeakerType::class, $speaker);

        return $this->render(':event/cfp:cfp.html.twig', ['event' => $event, 'form' => $form->createView()]);
    }
}
