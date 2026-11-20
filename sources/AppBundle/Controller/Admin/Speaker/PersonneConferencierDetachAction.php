<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Détache une ligne conférencier de sa personne. La personne est conservée, même vide.
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class PersonneConferencierDetachAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $speaker = $this->speakerRepository->get($request->request->getInt('speakerId'));
        if ($speaker === null) {
            throw $this->createNotFoundException('Speaker non trouvé');
        }
        $personneId = $speaker->getPersonneId();
        if ($personneId === null) {
            return $this->redirectToRoute('admin_speaker_edit', ['id' => $speaker->getId()]);
        }
        if (!$this->isCsrfTokenValid('admin_speaker_personne', $request->request->getString('_token'))) {
            $this->addFlash('error', 'Token invalide');

            return $this->redirectToRoute('admin_speaker_personne', ['id' => $personneId]);
        }

        $speaker->setPersonneId(null);
        $this->speakerRepository->save($speaker);
        $this->audit->log('Détachement du conférencier ' . $speaker->getId() . ' de la personne ' . $personneId);
        $this->addFlash('notice', 'Le conférencier a été détaché de la personne');

        return $this->redirectToRoute('admin_speaker_personne', ['id' => $personneId]);
    }
}
