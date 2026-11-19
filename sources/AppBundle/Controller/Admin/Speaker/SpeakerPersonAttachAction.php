<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Entity\Repository\SpeakerPersonRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Rattache une ligne conférencier à une personne existante.
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class SpeakerPersonAttachAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly SpeakerPersonRepository $speakerPersonRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $speaker = $this->speakerRepository->get($request->request->getInt('speakerId'));
        $person = $this->speakerPersonRepository->find($request->request->getInt('personId'));
        if ($speaker === null || $person === null) {
            throw $this->createNotFoundException('Speaker ou personne non trouvé');
        }
        if (!$this->isCsrfTokenValid('admin_speaker_person', $request->request->getString('_token'))) {
            $this->addFlash('error', 'Token invalide');

            return $this->redirectToRoute('admin_speaker_person', ['id' => $person->id]);
        }

        $speaker->setPersonId($person->id);
        $this->speakerRepository->save($speaker);
        $this->audit->log('Rattachement du conférencier ' . $speaker->getId() . ' à la personne ' . $person->slug);
        $this->addFlash('notice', 'Le conférencier a été rattaché à la personne');

        return $this->redirectToRoute('admin_speaker_person', ['id' => $person->id]);
    }
}
