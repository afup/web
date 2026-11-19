<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Entity\Repository\SpeakerPersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Fusionne une personne dans une autre : ses lignes sont déplacées, puis elle est supprimée (son slug est perdu).
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class SpeakerPersonMergeAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerPersonRepository $speakerPersonRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $target = $this->speakerPersonRepository->find($request->request->getInt('id'));
        $source = $this->speakerPersonRepository->find($request->request->getInt('sourceId'));
        if ($target === null || $source === null) {
            throw $this->createNotFoundException('Personne non trouvée');
        }
        if (!$this->isCsrfTokenValid('admin_speaker_person', $request->request->getString('_token')) || $target->id === $source->id) {
            $this->addFlash('error', 'Fusion impossible');

            return $this->redirectToRoute('admin_speaker_person', ['id' => $target->id]);
        }

        $sourceSlug = $source->slug;
        $this->speakerPersonRepository->merge($target, $source);
        $this->audit->log('Fusion de la personne ' . $sourceSlug . ' dans ' . $target->slug);
        $this->addFlash('notice', 'La personne ' . $sourceSlug . ' a été fusionnée dans ' . $target->slug);

        return $this->redirectToRoute('admin_speaker_person', ['id' => $target->id]);
    }
}
