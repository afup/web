<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\TalkType;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Talk\TalkFormHandler;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProposeAction extends AbstractController
{
    private SpeakerFactory $speakerFactory;
    private TranslatorInterface $translator;
    private TalkFormHandler $talkFormHandler;
    private SidebarRenderer $sidebarRenderer;
    private EventActionHelper $eventActionHelper;

    public function __construct(
        TalkFormHandler $talkFormHandler,
        SpeakerFactory $speakerFactory,
        TranslatorInterface $translator,
        SidebarRenderer $sidebarRenderer,
        EventActionHelper $eventActionHelper
    ) {
        $this->speakerFactory = $speakerFactory;
        $this->translator = $translator;
        $this->talkFormHandler = $talkFormHandler;
        $this->sidebarRenderer = $sidebarRenderer;
        $this->eventActionHelper = $eventActionHelper;
    }

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        if ($event->getDateEndCallForPapers() < new DateTime()) {
            return $this->render('event/cfp/closed.html.twig', [
                'event' => $event
            ]);
        }
        $speaker = $this->speakerFactory->getSpeaker($event);
        if ($speaker->getId() === null) {
            $this->addFlash('error', $this->translator->trans('Vous devez remplir votre profil conférencier afin de pouvoir soumettre un sujet.'));

            return $this->redirectToRoute('cfp_speaker', [
                'eventSlug' => $event->getPath()
            ]);
        }

        $talk = new Talk();
        $talk->setForumId($event->getId());
        $form = $this->createForm(TalkType::class, $talk, [
            TalkType::IS_AFUP_DAY => $event->isAfupDay()
        ]);
        if ($this->talkFormHandler->handle($request, $event, $form, $speaker)) {
            $this->addFlash('success', $this->translator->trans('Proposition enregistrée !'));

            return $this->redirectToRoute('cfp_edit', [
                'eventSlug' => $event->getPath(),
                'talkId' => $talk->getId(),
            ]);
        }

        return $this->render('event/cfp/propose.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'talk' => $talk,
            'sidebar' => $this->sidebarRenderer->render($event),
        ]);
    }
}
