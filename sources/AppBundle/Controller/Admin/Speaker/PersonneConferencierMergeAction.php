<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Fusionne une personne dans une autre : ses lignes sont déplacées, puis elle est supprimée (son slug est perdu).
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class PersonneConferencierMergeAction extends AbstractController
{
    public function __construct(
        private readonly PersonneConferencierRepository $personneConferencierRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $target = $this->personneConferencierRepository->find($request->request->getInt('id'));
        $source = $this->personneConferencierRepository->find($request->request->getInt('sourceId'));
        if ($target === null || $source === null) {
            throw $this->createNotFoundException('Personne non trouvée');
        }
        if (!$this->isCsrfTokenValid('admin_speaker_personne', $request->request->getString('_token')) || $target->id === $source->id) {
            $this->addFlash('error', 'Fusion impossible');

            return $this->redirectToRoute('admin_speaker_personne', ['id' => $target->id]);
        }

        $sourceSlug = $source->slug;
        $this->personneConferencierRepository->merge($target, $source);
        $this->audit->log('Fusion de la personne ' . $sourceSlug . ' dans ' . $target->slug);
        $this->addFlash('notice', 'La personne ' . $sourceSlug . ' a été fusionnée dans ' . $target->slug);

        return $this->redirectToRoute('admin_speaker_personne', ['id' => $target->id]);
    }
}
