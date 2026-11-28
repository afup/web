<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
class SpeakerDeleteAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $speaker = $this->speakerRepository->find($request->query->getInt('id'));
        if (null === $speaker) {
            throw $this->createNotFoundException('Speaker non trouvé');
        }
        $speakerId = $speaker->id;
        $eventId = $speaker->eventId;
        try {
            $this->speakerRepository->delete($speaker);
            $this->audit->log('Suppression du conférencier ' . $speakerId);
            $this->addFlash('notice', 'Le conférencier a été supprimé');
        } catch (Exception) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du conférencier');
        }

        return $this->redirectToRoute('admin_speaker_list', [
            'eventId' => $eventId,
        ]);
    }
}
