<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventThemeRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventThemeAction extends AbstractController
{
    public function __construct(
        private readonly EventThemeRepository $eventThemeRepository,
        private readonly EventActionHelper $eventActionHelper,
        private readonly EventSelectFactory $eventSelectFactory,
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);

        // Handle AJAX requests for updating data
        if ($request->isXmlHttpRequest()) {
            return $this->handleAjaxRequest($request, $event);
        }
        if ($request->getMethod() === 'POST' && $request->request->has('delete')) {
            $theme = $this->eventThemeRepository->get($request->request->getInt('theme_id'));
            $this->eventThemeRepository->delete($theme);
            $this->addFlash('notice', sprintf('Le thème "%s" a été supprimé.', $theme->getName()));
            return $this->redirectToRoute('admin_event_themes_list');
        }

        $themes = $this->eventThemeRepository->getByEventOrderedByPriority($event->getId());
        $scheduledTalks = $this->talkRepository->getScheduledTalksByEvent($event->getId());

        return $this->render('admin/event/theme_list.html.twig', [
            'themes' => $themes,
            'scheduled_talks' => $scheduledTalks,
            'event' => $event,
            'event_select_form' => $this->eventSelectFactory->create($event, $request)->createView(),
        ]);
    }

    private function handleAjaxRequest(Request $request, Event $event): JsonResponse
    {
        $action = $request->request->get('action');

        return match ($action) {
            'update_theme_priority' => $this->updateThemePriority($request),
            'update_talk_theme' => $this->updateTalkTheme($request),
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

    private function updateTalkTheme(Request $request): JsonResponse
    {
        $talkId = $request->request->getInt('talk_id');
        $themeId = $request->request->get('theme_id');

        $talk = $this->talkRepository->get($talkId);
        if (!$talk) {
            return new JsonResponse(['error' => 'Conférence non trouvée'], 404);
        }

        $talk->setTheme($themeId ? (int) $themeId : null);
        $this->talkRepository->save($talk);

        return new JsonResponse(['success' => true]);
    }
}
