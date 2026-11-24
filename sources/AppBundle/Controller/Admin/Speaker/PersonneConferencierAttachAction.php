<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Rattache une ligne conférencier à une personne existante.
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class PersonneConferencierAttachAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly PersonneConferencierRepository $personneConferencierRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $speaker = $this->speakerRepository->get($request->request->getInt('speakerId'));
        $personne = $this->personneConferencierRepository->find($request->request->getInt('personneId'));
        if ($speaker === null || $personne === null) {
            throw $this->createNotFoundException('Speaker ou personne non trouvé');
        }
        if (!$this->isCsrfTokenValid('admin_speaker_personne', $request->request->getString('_token'))) {
            $this->addFlash('error', 'Token invalide');

            return $this->redirectToRoute('admin_speaker_personne', ['id' => $personne->id]);
        }

        // Une personne n'a qu'une fiche par édition.
        $existingId = $this->personneConferencierRepository->findSpeakerIdInEvent($personne->id, (int) $speaker->getEventId());
        if ($existingId !== null && $existingId !== $speaker->getId()) {
            $this->addFlash('error', 'Cette personne a déjà une fiche dans cette édition');

            return $this->redirectToRoute('admin_speaker_personne', ['id' => $personne->id]);
        }

        $speaker->setPersonneId($personne->id);
        $this->speakerRepository->save($speaker);
        $this->audit->log('Rattachement du conférencier ' . $speaker->getId() . ' à la personne ' . $personne->slug);
        $this->addFlash('notice', 'Le conférencier a été rattaché à la personne');

        return $this->redirectToRoute('admin_speaker_personne', ['id' => $personne->id]);
    }
}
