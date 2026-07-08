<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventThemeAction extends AbstractController
{
    public function __construct(private readonly EventThemeRepository $eventThemeRepository) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;

        // Handle AJAX requests for updating data
        if ($request->isXmlHttpRequest()) {
            return $this->handleAjaxRequest($request, $event);
        }
        if ($request->getMethod() === 'POST' && $request->request->has('delete')) {
            $theme = $this->eventThemeRepository->get($request->request->getInt('theme_id'));
            if ($theme === null) {
                $this->addFlash('error', 'Thème introuvable.');
            } else {
                $name = $theme->getName();
                $this->eventThemeRepository->delete($theme);
                $this->addFlash('notice', sprintf('Le thème "%s" a été supprimé.', $name));
            }
            return $this->redirectToRoute('admin_event_themes_list');
        }

        $eventId = $event->getId() ?? 0;
        $themes = $this->eventThemeRepository->getByThemesOrderedByPriority($eventId);

        return $this->render('admin/event/theme_list.html.twig', [
            'themes' => $themes,
            'event' => $event,
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }

    private function handleAjaxRequest(Request $request, Event $event): JsonResponse
    {
        $action = $request->request->get('action');

        return match ($action) {
            'update_theme_priority' => $this->updateThemePriority($request),
            default => new JsonResponse(['error' => 'Action non reconnue'], 400),
        };
    }

    private function updateThemePriority(Request $request): JsonResponse
    {
        $themeId = $request->request->getInt('theme_id');
        $priority = $request->request->getInt('priority');

        $theme = $this->eventThemeRepository->get($themeId);
        if (!$theme) {
            return new JsonResponse(['error' => 'Thème non trouvé'], 404);
        }

        $theme->setPriority($priority);
        $this->eventThemeRepository->save($theme);

        return new JsonResponse(['success' => true]);
    }
}
