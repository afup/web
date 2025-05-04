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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProposeAction extends AbstractController
{
    public function __construct(
        private readonly TalkFormHandler $talkFormHandler,
        private readonly SpeakerFactory $speakerFactory,
        private readonly TranslatorInterface $translator,
        private readonly SidebarRenderer $sidebarRenderer,
        private readonly EventActionHelper $eventActionHelper,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        if ($event->getDateEndCallForPapers() < new DateTime()) {
            return $this->render('event/cfp/closed.html.twig', [
                'event' => $event,
            ]);
        }
        $speaker = $this->speakerFactory->getSpeaker($event);
        if ($speaker->getId() === null) {
            $this->addFlash('error', $this->translator->trans('Vous devez remplir votre profil conférencier afin de pouvoir soumettre un sujet.'));

            return $this->redirectToRoute('cfp_speaker', [
                'eventSlug' => $event->getPath(),
            ]);
        }

        $talk = new Talk();
        $talk->setForumId($event->getId());
        $form = $this->createForm(TalkType::class, $talk, [
            TalkType::IS_AFUP_DAY => $event->isAfupDay(),
        ]);
        if ($event->isCfpOpen()) {
            $form->add('save', SubmitType::class, ['label' => 'Sauvegarder']);
        } else {
            $form->add('save', SubmitType::class, ['label' => 'CFP fermé', 'attr' => ['disabled' => 'disabled']]);
        }

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
