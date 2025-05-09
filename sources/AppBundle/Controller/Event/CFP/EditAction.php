<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\TalkInvitationType;
use AppBundle\Event\Form\TalkType;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkInvitationRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TalkInvitation;
use AppBundle\Event\Talk\InvitationFormHandler;
use AppBundle\Event\Talk\TalkFormHandler;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly TalkRepository $talkRepository,
        private readonly TalkInvitationRepository $talkInvitationRepository,
        private readonly TalkFormHandler $talkFormHandler,
        private readonly InvitationFormHandler $invitationFormHandler,
        private readonly SpeakerFactory $speakerFactory,
        private readonly TranslatorInterface $translator,
        private readonly SpeakerRepository $speakerRepository,
        private readonly VoteRepository $voteRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly SidebarRenderer $sidebarRenderer,
    ) {
    }

    public function __invoke(Request $request)
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        $user = $this->eventActionHelper->getUser();
        if ($event->getDateEndCallForPapers() < new DateTime()) {
            return $this->render('event/cfp/closed.html.twig', ['event' => $event]);
        }
        $speaker = $this->speakerFactory->getSpeaker($event);
        if ($speaker->getId() === null) {
            $this->addFlash('error', $this->translator->trans('Vous devez remplir votre profil conférencier afin de pouvoir soumettre un sujet.'));

            return $this->redirectToRoute('cfp_speaker', [
                'eventSlug' => $event->getPath(),
            ]);
        }
        $talkId = (int) $request->attributes->get('talkId');
        /** @var Talk $talk */
        $talk = $this->talkRepository->getOneBy(['id' => $talkId, 'forumId' => $event->getId()]);
        if ($talk === null) {
            throw $this->createNotFoundException(sprintf('Talk %d not found', $talkId));
        }

        if (!$this->authorizationChecker->isGranted('edit', $talk)) {
            $exception = new AccessDeniedException();
            $exception->setAttributes('edit');
            $exception->setSubject($talk);

            throw $exception;
        }

        $talkForm = $this->createForm(TalkType::class, $talk, [TalkType::OPT_COC_CHECKED => true]);
        $talkForm->add('save', SubmitType::class, ['label' => 'Sauvegarder']);
        if ($this->talkFormHandler->handle($request, $event, $talkForm, $speaker)) {
            $this->addFlash('success', $this->translator->trans('Proposition enregistrée !'));

            return $this->redirectToRoute('cfp_edit', [
                'eventSlug' => $event->getPath(),
                'talkId' => $talk->getId(),
            ]);
        }
        $invitationForm = $this->createInvitationForm($user, $talk);
        $invitationSent = $this->invitationFormHandler->handle($request, $event, $invitationForm, $user, $talk);
        if ($invitationSent) {
            $this->addFlash('success', $this->translator->trans('Invitation envoyée !'));
        }

        return $this->render('event/cfp/edit.html.twig', [
                'event' => $event,
                'form' => $talkForm->createView(),
                'talk' => $talk,
                'invitations' => $this->talkInvitationRepository->getPendingInvitationsByTalkId($talk->getId()),
                'speakers' => $this->speakerRepository->getSpeakersByTalk($talk),
                'invitationForm' => $invitationForm->createView(),
                'votes' => $this->voteRepository->getVotesByTalkWithUser($talk->getId()),
                'sidebar' => $this->sidebarRenderer->render($event),
            ]
        );
    }

    private function createInvitationForm(GithubUser $user, Talk $talk): FormInterface
    {
        $invitation = new TalkInvitation();
        $invitation
            ->setSubmittedBy($user->getId())
            ->setSubmittedOn(new DateTime())
            ->setToken(base64_encode(random_bytes(30)))
            ->setState(TalkInvitation::STATE_PENDING)
            ->setTalkId($talk->getId());

        return $this->createForm(TalkInvitationType::class, $invitation);
    }
}
