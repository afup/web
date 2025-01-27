<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SpeakerLinkGravatarAction
{
    private SpeakerRepository $speakerRepository;
    private PhotoStorage $photoStorage;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SpeakerRepository $speakerRepository,
        PhotoStorage $photoStorage,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->speakerRepository = $speakerRepository;
        $this->photoStorage = $photoStorage;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var Speaker|null $speaker */
        $speaker = $this->speakerRepository->get($request->query->get('id'));
        if (null === $speaker) {
            throw new NotFoundHttpException('Speaker non trouvé');
        }
        $speaker->setPhoto($this->photoStorage->storeFromGravatar($speaker));
        if (null !== $speaker->getPhoto()) {
            $this->speakerRepository->save($speaker);
            $this->flashBag->add('notice', 'L\'image gravatar a été associée');
        } else {
            $this->flashBag->add('error', 'Erreur lors de la récupération de l\'image');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_speaker_edit', ['id' => $speaker->getId()]));
    }
}
