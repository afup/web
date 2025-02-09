<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\TalkInvitationRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TalkInvitation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class InviteAction
{
    private TalkRepository $talkRepository;
    private UrlGeneratorInterface $urlGenerator;
    private SpeakerFactory $speakerFactory;
    private SessionInterface $session;
    private TranslatorInterface $translator;
    private TalkInvitationRepository $talkInvitationRepository;
    private TalkToSpeakersRepository $talkToSpeakersRepository;
    private EventActionHelper $eventActionHelper;
    private FlashBagInterface $flashBag;

    public function __construct(
        EventActionHelper $eventActionHelper,
        TalkRepository $talkRepository,
        UrlGeneratorInterface $urlGenerator,
        SpeakerFactory $speakerFactory,
        TalkInvitationRepository $talkInvitationRepository,
        TalkToSpeakersRepository $talkToSpeakersRepository,
        SessionInterface $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->talkRepository = $talkRepository;
        $this->urlGenerator = $urlGenerator;
        $this->speakerFactory = $speakerFactory;
        $this->session = $session;
        $this->translator = $translator;
        $this->talkInvitationRepository = $talkInvitationRepository;
        $this->talkToSpeakersRepository = $talkToSpeakersRepository;
        $this->eventActionHelper = $eventActionHelper;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        $token = $request->attributes->get('token');
        $talkId = (int) $request->attributes->get('talkId');
        /** @var TalkInvitation $invitation */
        $invitation = $this->talkInvitationRepository->get(['talk_id' => $talkId, 'token' => $token]);
        /** @var Talk $talk */
        $talk = $this->talkRepository->get($talkId);
        if ($invitation === null || $talk === null || $talk->getForumId() !== $event->getId()) {
            throw new NotFoundHttpException('Invitation or talk not found');
        }
        $speaker = $this->speakerFactory->getSpeaker($event);
        if ($speaker->getId() === null) {
            $this->flashBag->add('error', $this->translator->trans('Vous devez remplir votre profil conférencier afin de pouvoir accepter une invitation.'));
            $this->session->set('pendingInvitation', ['talkId' => $talkId, 'token' => $token, 'eventSlug' => $event->getPath()]);

            return new RedirectResponse($this->urlGenerator->generate('cfp_speaker', ['eventSlug' => $event->getPath()]));
        }

        if ($invitation->getState() === TalkInvitation::STATE_PENDING) {
            $invitation->setState(TalkInvitation::STATE_ACCEPTED);
            $this->flashBag->add('success', $this->translator->trans('Vous etes désormais co-conférencier !'));
            // Save co-speaker
            $this->talkInvitationRepository->save($invitation);
            $this->talkToSpeakersRepository->addSpeakerToTalk($talk, $speaker);
        }

        return new RedirectResponse($this->urlGenerator->generate('cfp_edit', [
            'eventSlug' => $event->getPath(),
            'talkId' => $talkId,
        ]));
    }
}
