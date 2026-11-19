<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Speaker\SpeakerPersonLinker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Crée la personne d'une ligne conférencier qui n'est rattachée à rien.
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class SpeakerPersonCreateAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly SpeakerPersonLinker $speakerPersonLinker,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $speaker = $this->speakerRepository->get($request->request->getInt('speakerId'));
        if ($speaker === null) {
            throw $this->createNotFoundException('Speaker non trouvé');
        }
        if (!$this->isCsrfTokenValid('admin_speaker_person', $request->request->getString('_token'))) {
            $this->addFlash('error', 'Token invalide');

            return $this->redirectToRoute('admin_speaker_edit', ['id' => $speaker->getId()]);
        }
        if ($speaker->getPersonId() !== null) {
            return $this->redirectToRoute('admin_speaker_person', ['id' => $speaker->getPersonId()]);
        }

        $person = $this->speakerPersonLinker->createPerson($speaker->getFirstname() . ' ' . $speaker->getLastname());
        $speaker->setPersonId($person->id);
        $this->speakerRepository->save($speaker);
        $this->audit->log('Création de la personne ' . $person->slug . ' pour le conférencier ' . $speaker->getId());
        $this->addFlash('notice', 'La personne a été créée');

        return $this->redirectToRoute('admin_speaker_person', ['id' => $person->id]);
    }
}
