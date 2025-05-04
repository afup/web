<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SpeakerDeleteAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(private SpeakerRepository $speakerRepository)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var Speaker|null $speaker */
        $speaker = $this->speakerRepository->get($request->query->get('id'));
        if (null === $speaker) {
            throw $this->createNotFoundException('Speaker non trouvé');
        }
        try {
            $this->speakerRepository->delete($speaker);
            $this->log('Suppression du conférencier ' . $speaker->getId());
            $this->addFlash('notice', 'Le conférencier a été supprimé');
        } catch (Exception) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du conférencier');
        }

        return $this->redirectToRoute('admin_speaker_list', [
            'eventId' => $speaker->getEventId(),
        ]);
    }
}
