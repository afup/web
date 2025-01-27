<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\TalkType;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Talk\TalkFormHandler;
use DateTime;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

class ProposeAction
{
    private UrlGeneratorInterface $urlGenerator;
    private \Twig_Environment $twig;
    private SpeakerFactory $speakerFactory;
    private FormFactoryInterface $formFactory;
    private TranslatorInterface $translator;
    private TalkFormHandler $talkFormHandler;
    private SidebarRenderer $sidebarRenderer;
    private EventActionHelper $eventActionHelper;
    private FlashBagInterface $flashBag;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        Twig_Environment $twig,
        FormFactoryInterface $formFactory,
        TalkFormHandler $talkFormHandler,
        SpeakerFactory $speakerFactory,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        SidebarRenderer $sidebarRenderer,
        EventActionHelper $eventActionHelper
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->speakerFactory = $speakerFactory;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
        $this->talkFormHandler = $talkFormHandler;
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
        if ($speaker->getId() === null) {
            $this->flashBag->add('error', $this->translator->trans('Vous devez remplir votre profil conférencier afin de pouvoir soumettre un sujet.'));

            return new RedirectResponse($this->urlGenerator->generate('cfp_speaker', ['eventSlug' => $event->getPath()]));
        }

        $talk = new Talk();
        $talk->setForumId($event->getId());
        $form = $this->formFactory->create(TalkType::class, $talk, [
            TalkType::IS_AFUP_DAY => $event->isAfupDay()
        ]);
        if ($this->talkFormHandler->handle($request, $event, $form, $speaker)) {
            $this->flashBag->add('success', $this->translator->trans('Proposition enregistrée !'));

            return new RedirectResponse($this->urlGenerator->generate('cfp_edit', [
                'eventSlug' => $event->getPath(),
                'talkId' => $talk->getId(),
            ]));
        }

        return new Response($this->twig->render('event/cfp/propose.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'talk' => $talk,
            'sidebar' => $this->sidebarRenderer->render($event),
        ]));
    }
}
