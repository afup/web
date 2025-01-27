<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SpeakerDeleteAction
{
    use DbLoggerTrait;

    private SpeakerRepository $speakerRepository;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SpeakerRepository $speakerRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->speakerRepository = $speakerRepository;
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
        try {
            $this->speakerRepository->delete($speaker);
            $this->log('Suppression du conférencier ' . $speaker->getId());
            $this->flashBag->add('notice', 'Le conférencier a été supprimé');
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression du conférencier');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_speaker_list', ['eventId' => $speaker->getEventId()]));
    }
}
