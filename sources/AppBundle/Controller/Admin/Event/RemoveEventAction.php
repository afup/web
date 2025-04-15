<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RemoveEventAction extends AbstractController
{
    private EventRepository $eventRepository;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(EventRepository $eventRepository,
                                CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->eventRepository = $eventRepository;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function __invoke(int $id, string $token, Request $request): RedirectResponse
    {
        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('forum_delete', $token))) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('admin_event_list');
        }
        $result = $this->eventRepository->getList($id);
        if (count($result) !== 1) {
            $this->addFlash('error', 'Identifiant d\'évènement incorrect');
            return $this->redirectToRoute('admin_event_list');
        }

        if ($result[0]['est_supprimable'] === false) {
            $this->addFlash('error', 'Impossible de supprimer un évènement utilisé');
            return $this->redirectToRoute('admin_event_list');
        }

        $event = $this->eventRepository->get($id);
        $this->eventRepository->delete($event);

        $this->addFlash('notice', 'Événement supprimé');
        return $this->redirectToRoute('admin_event_list');
    }
}
