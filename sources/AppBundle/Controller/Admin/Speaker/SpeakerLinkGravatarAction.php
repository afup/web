<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SpeakerLinkGravatarAction extends AbstractController
{
    private SpeakerRepository $speakerRepository;
    private PhotoStorage $photoStorage;

    public function __construct(
        SpeakerRepository $speakerRepository,
        PhotoStorage $photoStorage
    ) {
        $this->speakerRepository = $speakerRepository;
        $this->photoStorage = $photoStorage;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var Speaker|null $speaker */
        $speaker = $this->speakerRepository->get($request->query->get('id'));
        if (null === $speaker) {
            throw $this->createNotFoundException('Speaker non trouvé');
        }
        $speaker->setPhoto($this->photoStorage->storeFromGravatar($speaker));
        if (null !== $speaker->getPhoto()) {
            $this->speakerRepository->save($speaker);
            $this->addFlash('notice', 'L\'image gravatar a été associée');
        } else {
            $this->addFlash('error', 'Erreur lors de la récupération de l\'image');
        }

        return $this->redirectToRoute('admin_speaker_edit', [
            'id' => $speaker->getId()
        ]);
    }
}
