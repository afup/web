<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Speaker\PersonneConferencierLinker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Crée la personne d'une ligne conférencier qui n'est rattachée à rien.
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class PersonneConferencierCreateAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly PersonneConferencierLinker $personneConferencierLinker,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $speaker = $this->speakerRepository->get($request->request->getInt('speakerId'));
        if ($speaker === null) {
            throw $this->createNotFoundException('Speaker non trouvé');
        }
        if (!$this->isCsrfTokenValid('admin_speaker_personne', $request->request->getString('_token'))) {
            $this->addFlash('error', 'Token invalide');

            return $this->redirectToRoute('admin_speaker_edit', ['id' => $speaker->getId()]);
        }
        if ($speaker->getPersonneId() !== null) {
            return $this->redirectToRoute('admin_speaker_personne', ['id' => $speaker->getPersonneId()]);
        }

        $personne = $this->personneConferencierLinker->createPersonne($speaker->getFirstname() . ' ' . $speaker->getLastname());
        $speaker->setPersonneId($personne->id);
        $this->speakerRepository->save($speaker);
        $this->audit->log('Création de la personne ' . $personne->slug . ' pour le conférencier ' . $speaker->getId());
        $this->addFlash('notice', 'La personne a été créée');

        return $this->redirectToRoute('admin_speaker_personne', ['id' => $personne->id]);
    }
}
