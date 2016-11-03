<?php


namespace AppBundle\Controller;


use Afup\Site\Utils\Mail;
use AppBundle\CFP\PhotoStorage;
use AppBundle\Form\SpeakerType;
use AppBundle\Form\TalkInvitationType;
use AppBundle\Form\TalkType;
use AppBundle\Model\Event;
use AppBundle\Model\Repository\SpeakerRepository;
use AppBundle\Model\Repository\TalkInvitationRepository;
use AppBundle\Model\Repository\TalkRepository;
use AppBundle\Model\Repository\TalkToSpeakersRepository;
use AppBundle\Model\Talk;
use AppBundle\Model\TalkInvitation;
use CCMBenchmark\Ting\Driver\QueryException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
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
            $speakerRepository->save($speaker);
            $file = $speaker->getPhoto();
            $fileName = $this->get('app.photo_storage')->store($file, $speaker);
            $speaker->setPhoto($fileName);
            $speakerRepository->save($speaker);

            $this->addFlash('success', 'Profil sauvegardé.');
            if ($this->get('session')->has('pendingInvitation') === true) {
                $url = $this->generateUrl('cfp_invite', $this->get('session')->get('pendingInvitation'));
                $this->get('session')->remove('pendingInvitation');
            } else {
                $url = $this->generateUrl('cfp_speaker', ['eventSlug' => $eventSlug]);
            }
            return new RedirectResponse($url);
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
        /**
         * @var $talkRepository TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        /**
         * @var $speakerRepository SpeakerRepository
         */
        $speakerRepository = $this->get('ting')->get(SpeakerRepository::class);

        /**
         * @var $talk Talk
         */
        $talk = $talkRepository->getOneBy(['id' => $talkId, 'forumId' => $event->getId()]);

        if ($talk === null) {
            throw $this->createNotFoundException(sprintf('Talk %i not found', $talkId));
        }
        $this->denyAccessUnlessGranted('edit', $talk);

        $talkForm = $this->createForm(TalkType::class, $talk);
        $formResponse = $this->handleTalkForm($request, $event, $talkForm);
        if ($formResponse instanceof Response) {
            return $formResponse;
        }
        $invitation = new TalkInvitation();

        $invitation
            ->setSubmittedBy($this->getUser()->getId())
            ->setSubmittedOn(new \DateTime())
            ->setToken(base64_encode(random_bytes(30)))
            ->setState(TalkInvitation::STATE_PENDING)
            ->setTalkId($talkId)
        ;

        $invitationForm = $this->createForm(TalkInvitationType::class, $invitation);
        $invitationForm->handleRequest($request);

        if ($invitationForm->isSubmitted()) {
            /**
             * @var $invitation TalkInvitation
             */
            $invitation = $invitationForm->getData();

            if ($invitationForm->isValid()) {
                try {
                    $this->get('ting')->get(TalkInvitationRepository::class)->save($invitation);
                } catch (QueryException $exception) {
                    $invitationForm->addError(new FormError($exception->getMessage()));
                }
                $user = $this->getUser();
                // Send mail to the other guy, begging for him to join the talk
                $text = <<<MAIL
Bonjour !
{$user->getLogin()} vous invite à rejoindre sa conférence "{$talk->getTitle()}" en tant que co-conférencier.
Pour accepter, suivez ce lien: {$this->generateUrl('cfp_invite', ['eventSlug' => $eventSlug, 'talkId' => $talkId, 'token' => $invitation->getToken()])}
Sinon, ignorez tout simplement cet email.

L'équipe afup.
MAIL;
                $mail = new Mail();
                $mail->sendSimpleMessage('CFP Afup', $text, [$invitation->getEmail()]);
                dump($text);
                die;
            }
        }

        $invitations = $this->get('ting')->get(TalkInvitationRepository::class)->getPendingInvitationsByTalkId($talk->getId());
        $speakers = $speakerRepository->getSpeakersByTalk($talk);

        return $this->render(
            ':event/cfp:edit.html.twig',
            [
                'event' => $event,
                'form' => $talkForm->createView(),
                'talk' => $talk,
                'invitations' => $invitations,
                'speakers' => $speakers,
                'invitationForm' => $invitationForm->createView()
            ]
        );
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

    public function inviteAction($eventSlug, $talkId, $token)
    {
        $event = $this->checkEventSlug($eventSlug);
        /**
         * @var $talkInvitationRepository TalkInvitationRepository
         */
        $talkInvitationRepository = $this->get('ting')->get(TalkInvitationRepository::class);
        /**
         * @todo check event and talk matches here
         */
        /**
         * @var $invitation TalkInvitation
         */
        $invitation = $talkInvitationRepository->get(['talk_id' => $talkId, 'token' => $token]);
        $talk = $this->get('ting')->get(TalkRepository::class)->get($talkId);

        if ($invitation === null || $talk === null) {
            throw $this->createNotFoundException('Invitation or talk not found');
        }

        $speaker = $this->get('app.speaker_factory')->getSpeaker($event);
        if ($speaker->getId() === null) {
            $this->addFlash('error', $this->get('translator')->trans('Vous devez remplir votre profil conférencier afin de pouvoir accepter une invitation.'));
            $this->get('session')->set('pendingInvitation', ['talkId' => $talkId, 'token' => $token, 'eventSlug' => $eventSlug]);

            return new RedirectResponse($this->generateUrl('cfp_speaker', ['eventSlug' => $event->getPath()]));
        }

        if ($invitation->getState() === TalkInvitation::STATE_PENDING) {
            $invitation->setState(TalkInvitation::STATE_ACCEPTED);
            $this->addFlash('success', $this->get('translator')->trans('Vous etes désormais co-conférencier !'));

            // Save cospeaker
            $talkInvitationRepository->save($invitation);
            $this->get('ting')->get(TalkToSpeakersRepository::class)->addSpeakerToTalk($talk, $speaker);


            return $this->redirectToRoute('cfp_edit', ['eventSlug' => $eventSlug, 'talkId' => $talkId]);
        }

        return $this->render(':event/cfp:invite.html.twig', ['event' => $event, 'invitation' => $invitation]);
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

                /* I should
                1 - save talk
                2 - Add current user as a speaker if it's a new talk
                3 - Not touch to speakers anymore
                */

                $talkRepository->saveWithSpeaker($talk, $this->get('app.speaker_factory')->getSpeaker($event));

                $this->addFlash('success', $this->get('translator')->trans('Proposition enregistrée !'));

                return $this->redirectToRoute('cfp_edit', ['eventSlug' => $event->getPath(), 'talkId' => $talk->getId()]);
            }
        }
        return null;
    }
}
