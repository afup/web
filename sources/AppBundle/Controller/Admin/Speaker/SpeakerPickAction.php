<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Webmozart\Assert\Assert;

/**
 * Recherche d'un conférencier d'une édition passée, à reprendre pour l'événement courant.
 */
#[IsGranted('ROLE_EVENT_WRITER')]
final class SpeakerPickAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly PersonneConferencierRepository $personneConferencierRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $event = $this->eventRepository->get($request->query->get('eventId'));
        Assert::notNull($event);
        $search = $request->query->getString('q');

        return $this->render('admin/speaker/pick.html.twig', [
            'event' => $event,
            'search' => $search,
            'results' => $this->personneConferencierRepository->searchAcrossEvents($search),
        ]);
    }
}
