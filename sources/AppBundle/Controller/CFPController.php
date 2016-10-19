<?php


namespace AppBundle\Controller;


use AppBundle\Form\SpeakerType;
use AppBundle\Model\GithubUser;
use AppBundle\Model\Repository\EventRepository;
use AppBundle\Model\Repository\SpeakerRepository;
use AppBundle\Model\Speaker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CFPController extends EventBaseController
{
    public function indexAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event/cfp:closed.html.twig', ['event' => $event]);
        }

        return $this->render(':event/cfp:home.html.twig', ['event' => $event]);
    }

    public function speakerAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event/cfp:closed.html.twig', ['event' => $event]);
        }

        /**
         * @var $speakerRepository SpeakerRepository
         */
        $speakerRepository = $this->get('ting')->get(SpeakerRepository::class);
        // Try to get a speaker for the current logged in user
        $speaker = $speakerRepository->getOneBy(['user' => $this->getUser()->getId()]);

        if ($speaker === null) {
            $speaker = new Speaker();
        }

        $speaker
            ->setEventId($event->getId())
            ->setUser($this->getUser()->getId())
        ;

        $form = $this->createForm(SpeakerType::class, $speaker);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /**
             * @var $speaker Speaker
             */
            $speaker = $form->getData();

            if ($form->isValid()) {
                $speakerRepository->save($speaker);
            }
        }

        return $this->render(':event/cfp:speaker.html.twig', ['event' => $event, 'form' => $form->createView()]);
    }

    public function editAction($eventSlug, $talkId)
    {

    }

    public function proposeAction($eventSlug)
    {

    }
}
