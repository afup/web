<?php

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SpeakerLinkGravatarAction
{
    /** @var EventActionHelper */
    private $eventActionHelper;
    /** @var SpeakerRepository */
    private $speakerRepository;
    /** @var PhotoStorage */
    private $photoStorage;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SpeakerRepository $speakerRepository,
        PhotoStorage $photoStorage,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->speakerRepository = $speakerRepository;
        $this->photoStorage = $photoStorage;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request)
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
