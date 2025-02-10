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
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

class EditAction
{
    private UrlGeneratorInterface $urlGenerator;
    private \Twig_Environment $twig;
    private SpeakerFactory $speakerFactory;
    private FormFactoryInterface $formFactory;
    private TranslatorInterface $translator;
    private TalkFormHandler $talkFormHandler;
    private TalkRepository $talkRepository;
    private TalkInvitationRepository $talkInvitationRepository;
    private SpeakerRepository $speakerRepository;
    private VoteRepository $voteRepository;
    private SidebarRenderer $sidebarRenderer;
    private EventActionHelper $eventActionHelper;
    private AuthorizationCheckerInterface $authorizationChecker;
    private InvitationFormHandler $invitationFormHandler;
    private FlashBagInterface $flashBag;

    public function __construct(
        EventActionHelper $eventActionHelper,
        UrlGeneratorInterface $urlGenerator,
        Twig_Environment $twig,
        FormFactoryInterface $formFactory,
        TalkRepository $talkRepository,
        TalkInvitationRepository $talkInvitationRepository,
        TalkFormHandler $talkFormHandler,
        InvitationFormHandler $invitationFormHandler,
        SpeakerFactory $speakerFactory,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        SpeakerRepository $speakerRepository,
        VoteRepository $voteRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        SidebarRenderer $sidebarRenderer
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->speakerFactory = $speakerFactory;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
        $this->talkFormHandler = $talkFormHandler;
        $this->talkRepository = $talkRepository;
        $this->talkInvitationRepository = $talkInvitationRepository;
        $this->speakerRepository = $speakerRepository;
        $this->voteRepository = $voteRepository;
        $this->sidebarRenderer = $sidebarRenderer;
        $this->eventActionHelper = $eventActionHelper;
        $this->invitationFormHandler = $invitationFormHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request)
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        $user = $this->eventActionHelper->getUser();
        if ($event->getDateEndCallForPapers() < new DateTime()) {
            return new Response($this->twig->render('event/cfp/closed.html.twig', ['event' => $event]));
        }
        $speaker = $this->speakerFactory->getSpeaker($event);
        if ($speaker->getId() === null) {
            $this->flashBag->add('error', $this->translator->trans('Vous devez remplir votre profil conférencier afin de pouvoir soumettre un sujet.'));

            return new RedirectResponse($this->urlGenerator->generate('cfp_speaker', ['eventSlug' => $event->getPath()]));
        }
        $talkId = (int) $request->attributes->get('talkId');
        /** @var Talk $talk */
        $talk = $this->talkRepository->getOneBy(['id' => $talkId, 'forumId' => $event->getId()]);
        if ($talk === null) {
            throw new NotFoundHttpException(sprintf('Talk %d not found', $talkId));
        }

        if (!$this->authorizationChecker->isGranted('edit', $talk)) {
            $exception = new AccessDeniedException();
            $exception->setAttributes('edit');
            $exception->setSubject($talk);

            throw $exception;
        }

        $talkForm = $this->formFactory->create(TalkType::class, $talk, [TalkType::OPT_COC_CHECKED => true]);
        if ($this->talkFormHandler->handle($request, $event, $talkForm, $speaker)) {
            $this->flashBag->add('success', $this->translator->trans('Proposition enregistrée !'));

            return new RedirectResponse($this->urlGenerator->generate('cfp_edit', [
                'eventSlug' => $event->getPath(),
                'talkId' => $talk->getId(),
            ]));
        }
        $invitationForm = $this->createInvitationForm($user, $talk);
        $invitationSent = $this->invitationFormHandler->handle($request, $event, $invitationForm, $user, $talk);
        if ($invitationSent) {
            $this->flashBag->add('success', $this->translator->trans('Invitation envoyée !'));
        }

        return new Response($this->twig->render('event/cfp/edit.html.twig', [
                'event' => $event,
                'form' => $talkForm->createView(),
                'talk' => $talk,
                'invitations' => $this->talkInvitationRepository->getPendingInvitationsByTalkId($talk->getId()),
                'speakers' => $this->speakerRepository->getSpeakersByTalk($talk),
                'invitationForm' => $invitationForm->createView(),
                'votes' => $this->voteRepository->getVotesByTalkWithUser($talk->getId()),
                'sidebar' => $this->sidebarRenderer->render($event),
            ]
        ));
    }

    /**
     * @return FormInterface
     */
    private function createInvitationForm(GithubUser $user, Talk $talk)
    {
        $invitation = new TalkInvitation();
        $invitation
            ->setSubmittedBy($user->getId())
            ->setSubmittedOn(new DateTime())
            ->setToken(base64_encode(random_bytes(30)))
            ->setState(TalkInvitation::STATE_PENDING)
            ->setTalkId($talk->getId());

        return $this->formFactory->create(TalkInvitationType::class, $invitation);
    }
}
