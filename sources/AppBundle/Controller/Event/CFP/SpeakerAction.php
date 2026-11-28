<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\CFP;

use AppBundle\CFP\PhotoStorage;
use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\SpeakerType;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Speaker\PersonneConferencierLinker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpeakerAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly SpeakerFactory $speakerFactory,
        private readonly SpeakerRepository $speakerRepository,
        private readonly TranslatorInterface $translator,
        private readonly PhotoStorage $photoStorage,
        private readonly SidebarRenderer $sidebarRenderer,
        private readonly PersonneConferencierLinker $personneConferencierLinker,
    ) {}

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));

        if (!$event->isCfpOpen()) {
            return $this->render('event/cfp/closed.html.twig', [
                'event' => $event,
            ]);
        }

        $speaker = $this->speakerFactory->findSpeaker($event);
        $isNewSpeaker = $speaker === null;
        $speaker ??= $this->speakerFactory->createSpeaker($event);
        $previousSpeaker = null;
        $previousPhoto = null;
        // Nouveau profil prérempli depuis la dernière édition (voir SpeakerType) : sa photo, rangée par édition,
        // est copiée à l'enregistrement. Le nom provisoire satisfait la validation de Speaker.
        if ($isNewSpeaker && $speaker->githubUserId !== null) {
            $previousSpeaker = $this->speakerRepository->getFromLastEventAndUserId($speaker->eventId, $speaker->githubUserId);
            if ($previousSpeaker instanceof Speaker && $previousSpeaker->photo !== null) {
                $previousPhoto = $this->photoStorage->getUrl($previousSpeaker, PhotoStorage::DIR_ORIGINAL);
                if ($previousPhoto !== null) {
                    $speaker->photo = $previousSpeaker->photo;
                }
            }
        }
        $form = $this->createForm(SpeakerType::class, $speaker, [
            SpeakerType::OPT_PHOTO_REQUIRED => null === $speaker->photo,
            SpeakerType::OPT_PREVIOUS_SPEAKER => $previousSpeaker,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->personneConferencierLinker->ensurePersonne($speaker);
            $this->speakerRepository->save($speaker);

            $file = $speaker->photoFile;
            if ($file instanceof UploadedFile) {
                $speaker->photo = $this->photoStorage->store($file, $speaker);
                $this->speakerRepository->save($speaker);
            } elseif ($previousPhoto !== null && $previousSpeaker instanceof Speaker) {
                // En cas d'échec de la copie, la photo déjà renseignée est conservée.
                $speaker->photo = $this->photoStorage->copyFrom($previousSpeaker, $speaker) ?? $speaker->photo;
                $this->speakerRepository->save($speaker);
            }

            /** @var Session $session */
            $session = $request->getSession();
            $this->addFlash('success', $this->translator->trans('Profil sauvegardé.'));
            if ($session->has('pendingInvitation')) {
                $session->remove('pendingInvitation');
                return $this->redirectToRoute('cfp_invite', $session->get('pendingInvitation'));
            }

            return $this->redirectToRoute('cfp_speaker', [
                'eventSlug' => $event->getPath(),
            ]);
        }

        $photo = $previousPhoto;
        if (null === $photo && null !== $speaker->photo) {
            $photo = $this->photoStorage->getUrl($speaker, PhotoStorage::DIR_ORIGINAL);
        }

        return $this->render('event/cfp/speaker.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'photo' => $photo,
            'sidebar' => $this->sidebarRenderer->render($event),
        ]);
    }
}
