<?php

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\PhotoStorage;
use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use DateTime;
use Symfony\Component\Form\FormFactoryInterface;
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
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Twig_Environment */
    private $twig;
    /** @var SpeakerFactory */
    private $speakerFactory;
    /** @var PhotoStorage */
    private $photoStorage;
    /** @var SpeakerRepository */
    private $speakerRepository;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var SessionInterface */
    private $session;
    /** @var TranslatorInterface */
    private $translator;
    /** @var SidebarRenderer */
    private $sidebarRenderer;
    /** @var EventActionHelper */
    private $eventActionHelper;
    /** @var FlashBagInterface */
    private $flashBag;

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
            if ($file !== null) {
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
