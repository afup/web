<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
class SpeakerLinkGravatarAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly PhotoStorage $photoStorage,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $speaker = $this->speakerRepository->find($request->query->getInt('id'));
        if (!$speaker instanceof Speaker) {
            throw $this->createNotFoundException('Speaker non trouvé');
        }
        $photo = $this->photoStorage->storeFromGravatar($speaker);
        // Une photo déjà présente n'est jamais remplacée par une absence de photo.
        $speaker->photo = $photo ?? $speaker->photo;
        if (null !== $speaker->photo) {
            $this->speakerRepository->save($speaker);
            $this->addFlash('notice', 'L\'image gravatar a été associée');
        } else {
            $this->addFlash('error', 'Erreur lors de la récupération de l\'image');
        }

        return $this->redirectToRoute('admin_speaker_edit', [
            'id' => $speaker->id,
        ]);
    }
}
