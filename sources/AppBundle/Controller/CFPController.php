<?php


namespace AppBundle\Controller;


use AppBundle\CFP\PhotoStorage;
use AppBundle\Form\SpeakerType;
use AppBundle\Form\TalkType;
use AppBundle\Model\Event;
use AppBundle\Model\Repository\SpeakerRepository;
use AppBundle\Model\Repository\TalkRepository;
use AppBundle\Model\Talk;
use Symfony\Component\Form\Form;
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

        $talks = $this->get('ting')->get(TalkRepository::class)->getTalksBySpeaker($event, $this->get('app.speaker_factory')->getSpeaker($event));

        return $this->render(
            ':event/cfp:home.html.twig',
            [
                'event' => $event,
                'talks' => $talks,
                'speaker' => $this->get('app.speaker_factory')->getSpeaker($event),
                'photoStorage' => $this->get('app.photo_storage')
            ]
        );
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
        $speaker = $this->get('app.speaker_factory')->getSpeaker($event);

        $form = $this->createForm(SpeakerType::class, $speaker);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $speaker->getPhoto();
            $fileName = $this->get('app.photo_storage')->store($file, $speaker);
            $speaker->setPhoto($fileName);

            $speakerRepository->save($speaker);
        }

        $photo = null;
        if (!empty($speaker->getPhoto())) {
            $photo = $this->get('app.photo_storage')->getUrl($speaker, PhotoStorage::DIR_ORIGINAL);
        }

        return $this->render(':event/cfp:speaker.html.twig', ['event' => $event, 'form' => $form->createView(), 'photo' => $photo]);
    }

    public function editAction($eventSlug, $talkId, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event/cfp:closed.html.twig', ['event' => $event]);
        }
        $speaker = $this->get('app.speaker_factory')->getSpeaker($event);
        if ($speaker->getId() === null) {
            $this->addFlash('error', $this->get('translator')->trans('Vous devez remplir votre profil conférencier afin de pouvoir soumettre un sujet.'));
            return new RedirectResponse($this->generateUrl('cfp_speaker', ['eventSlug' => $event->getPath()]));
        }

        $talk = $this->get('ting')->get(TalkRepository::class)->getOneBy(['id' => $talkId, 'forumId' => $event->getId()]);

        if ($talk === null) {
            throw $this->createNotFoundException(sprintf('Talk %i not found', $talkId));
        }
        $this->denyAccessUnlessGranted('edit', $talk);

        $form = $this->createForm(TalkType::class, $talk);
        $formResponse = $this->handleTalkForm($request, $event, $form);
        if ($formResponse instanceof Response) {
            return $formResponse;
        }

        return $this->render(':event/cfp:propose.html.twig', ['event' => $event, 'form' => $form->createView(), 'talk' => $talk]);
    }

    public function proposeAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event/cfp:closed.html.twig', ['event' => $event]);
        }
        $speaker = $this->get('app.speaker_factory')->getSpeaker($event);
        if ($speaker->getId() === null) {
            $this->addFlash('error', $this->get('translator')->trans('Vous devez remplir votre profil conférencier afin de pouvoir soumettre un sujet.'));
            return new RedirectResponse($this->generateUrl('cfp_speaker', ['eventSlug' => $event->getPath()]));
        }

        $talk = new Talk();
        $talk->setForumId($event->getId());

        $form = $this->createForm(TalkType::class, $talk);
        $formResponse = $this->handleTalkForm($request, $event, $form);
        if ($formResponse instanceof Response) {
            return $formResponse;
        }

        return $this->render(':event/cfp:propose.html.twig', ['event' => $event, 'form' => $form->createView(), 'talk' => $talk]);
    }

    public function sidebarAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return new Response('');
        }

        $talks = $this->get('ting')->get(TalkRepository::class)->getTalksBySpeaker($event, $this->get('app.speaker_factory')->getSpeaker($event));
        return $this->render(':event/cfp:sidebar.html.twig', ['talks' => $talks, 'event' => $event]);
    }

    /**
     * @param Request $request
     * @param Event $event
     * @param Form $form
     * @return RedirectResponse|null
     */
    private function handleTalkForm(Request $request, Event $event, Form $form)
    {
        $talkRepository = $this->get('ting')->get(TalkRepository::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /**
             * @var $talk Talk
             */
            $talk = $form->getData();

            if ($form->isValid()) {
                $talk->setSubmittedOn(new \DateTime());
                $this->get('ting')->get(SpeakerRepository::class)->save($this->get('app.speaker_factory')->getSpeaker($event));
                $talkRepository->saveWithSpeaker($talk, $this->get('app.speaker_factory')->getSpeaker($event));

                $this->addFlash('success', $this->get('translator')->trans('Proposition enregistrée !'));

                return $this->redirectToRoute('cfp_edit', ['eventSlug' => $event->getPath(), 'talkId' => $talk->getId()]);
            }
        }
        return null;
    }
}
