<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\PhotoStorage;
use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use DateTime;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

class SpeakerAction
{
    private UrlGeneratorInterface $urlGenerator;
    private \Twig_Environment $twig;
    private SpeakerFactory $speakerFactory;
    private PhotoStorage $photoStorage;
    private SpeakerRepository $speakerRepository;
    private FormFactoryInterface $formFactory;
    private SessionInterface $session;
    private TranslatorInterface $translator;
    private SidebarRenderer $sidebarRenderer;
    private EventActionHelper $eventActionHelper;
    private FlashBagInterface $flashBag;

    public function __construct(
        EventActionHelper $eventActionHelper,
        UrlGeneratorInterface $urlGenerator,
        Twig_Environment $twig,
        FormFactoryInterface $formFactory,
        SpeakerFactory $speakerFactory,
        SpeakerRepository $speakerRepository,
        SessionInterface $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        PhotoStorage $photoStorage,
        SidebarRenderer $sidebarRenderer
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->speakerFactory = $speakerFactory;
        $this->photoStorage = $photoStorage;
        $this->speakerRepository = $speakerRepository;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->translator = $translator;
        $this->sidebarRenderer = $sidebarRenderer;
        $this->eventActionHelper = $eventActionHelper;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request)
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        if ($event->getDateEndCallForPapers() < new DateTime()) {
            return new Response($this->twig->render('event/cfp/closed.html.twig', ['event' => $event]));
        }
        $speaker = $this->speakerFactory->getSpeaker($event);

        $form = $this->formFactory->create(SpeakerType::class, $speaker, [
            SpeakerType::OPT_PHOTO_REQUIRED => null === $speaker->getPhoto(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->speakerRepository->save($speaker);

            $file = $speaker->getPhotoFile();
            if ($file instanceof UploadedFile) {
                $fileName = $this->photoStorage->store($file, $speaker);
                $speaker->setPhoto($fileName);
                $this->speakerRepository->save($speaker);
            }

            $this->flashBag->add('success', $this->translator->trans('Profil sauvegardé.'));
            if ($this->session->has('pendingInvitation')) {
                $url = $this->urlGenerator->generate('cfp_invite', $this->session->get('pendingInvitation'));
                $this->session->remove('pendingInvitation');
            } else {
                $url = $this->urlGenerator->generate('cfp_speaker', ['eventSlug' => $event->getPath()]);
            }

            return new RedirectResponse($url);
        }

        $photo = null;
        if (null !== $speaker->getPhoto()) {
            $photo = $this->photoStorage->getUrl($speaker, PhotoStorage::DIR_ORIGINAL);
        }

        return new Response($this->twig->render('event/cfp/speaker.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'photo' => $photo,
            'sidebar' => $this->sidebarRenderer->render($event),
        ]));
    }
}
