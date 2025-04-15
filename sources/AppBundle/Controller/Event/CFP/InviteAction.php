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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class InviteAction extends AbstractController
{
    private TalkRepository $talkRepository;
    private SpeakerFactory $speakerFactory;
    private TranslatorInterface $translator;
    private TalkInvitationRepository $talkInvitationRepository;
    private TalkToSpeakersRepository $talkToSpeakersRepository;
    private EventActionHelper $eventActionHelper;

    public function __construct(
        EventActionHelper $eventActionHelper,
        TalkRepository $talkRepository,
        SpeakerFactory $speakerFactory,
        TalkInvitationRepository $talkInvitationRepository,
        TalkToSpeakersRepository $talkToSpeakersRepository,
        TranslatorInterface $translator
    ) {
        $this->talkRepository = $talkRepository;
        $this->speakerFactory = $speakerFactory;
        $this->translator = $translator;
        $this->talkInvitationRepository = $talkInvitationRepository;
        $this->talkToSpeakersRepository = $talkToSpeakersRepository;
        $this->eventActionHelper = $eventActionHelper;
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
            throw $this->createNotFoundException('Invitation or talk not found');
        }
        $speaker = $this->speakerFactory->getSpeaker($event);

        if ($speaker->getId() === null) {
            $this->addFlash('error', $this->translator->trans('Vous devez remplir votre profil conférencier afin de pouvoir accepter une invitation.'));
            $this->addFlash('pendingInvitation', ['talkId' => $talkId, 'token' => $token, 'eventSlug' => $event->getPath()]);

            return $this->redirectToRoute('cfp_speaker', [
                'eventSlug' => $event->getPath()
            ]);
        }

        if ($invitation->getState() === TalkInvitation::STATE_PENDING) {
            $invitation->setState(TalkInvitation::STATE_ACCEPTED);
            $this->addFlash('success', $this->translator->trans('Vous etes désormais co-conférencier !'));
            // Save co-speaker
            $this->talkInvitationRepository->save($invitation);
            $this->talkToSpeakersRepository->addSpeakerToTalk($talk, $speaker);
        }

        return $this->redirectToRoute('cfp_edit', [
            'eventSlug' => $event->getPath(),
            'talkId' => $talkId,
        ]);
    }
}
