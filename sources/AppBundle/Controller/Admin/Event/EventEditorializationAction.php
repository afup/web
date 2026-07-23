<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventThemeRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventEditorializationAction extends AbstractController
{
    public function __construct(
        private readonly EventThemeRepository $eventThemeRepository,
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;

        if ($request->isXmlHttpRequest()) {
            return $this->handleAjaxRequest($request, $event);
        }

        $eventId = $event->getId() ?? 0;
        $hasThemes = $event->getHasThemes();
        $themes = $hasThemes ? iterator_to_array($this->eventThemeRepository->getByThemesOrderedByPriority($eventId)) : [];
        $scheduledTalks = iterator_to_array($this->talkRepository->getScheduledTalksByEvent($eventId));

        $talkGroups = $this->groupTalks($scheduledTalks, $themes, $hasThemes);

        return $this->render('admin/event/editorialization.html.twig', [
            'event' => $event,
            'event_select_form' => $eventSelection->selectForm(),
            'has_themes' => $hasThemes,
            'themes' => $themes,
            'talk_groups' => $talkGroups,
        ]);
    }

    /**
     * @param array<Talk> $scheduledTalks
     * @param array<\AppBundle\Event\Model\EventTheme> $themes
     * @return array<array{theme: ?\AppBundle\Event\Model\EventTheme, talks: array<Talk>}>
     */
    private function groupTalks(array $scheduledTalks, array $themes, bool $hasThemes): array
    {
        if (!$hasThemes) {
            $talks = $scheduledTalks;
            usort($talks, $this->compareTalks(...));

            return [['theme' => null, 'talks' => $talks]];
        }

        $talksByThemeId = [];
        foreach ($themes as $theme) {
            $talksByThemeId[(int) $theme->getId()] = [];
        }

        $noThemeTalks = [];
        foreach ($scheduledTalks as $talk) {
            $themeId = $talk->getTheme();
            if ($themeId !== null && isset($talksByThemeId[$themeId])) {
                $talksByThemeId[$themeId][] = $talk;
            } else {
                $noThemeTalks[] = $talk;
            }
        }

        usort($noThemeTalks, $this->compareTalks(...));
        $groups = [['theme' => null, 'talks' => $noThemeTalks]];

        foreach ($themes as $theme) {
            $talks = $talksByThemeId[(int) $theme->getId()];
            usort($talks, $this->compareTalks(...));
            $groups[] = ['theme' => $theme, 'talks' => $talks];
        }

        return $groups;
    }

    private function compareTalks(Talk $a, Talk $b): int
    {
        $positionA = $a->getPosition();
        $positionB = $b->getPosition();

        if ($positionA !== $positionB) {
            if ($positionA === null) {
                return 1;
            }
            if ($positionB === null) {
                return -1;
            }

            return $positionA <=> $positionB;
        }

        return $a->getTitle() <=> $b->getTitle();
    }

    private function handleAjaxRequest(Request $request, Event $event): JsonResponse
    {
        $action = $request->request->get('action');

        return match ($action) {
            'update_talk_theme' => $this->updateTalkTheme($request),
            'update_talk_position' => $this->updateTalkPosition($request),
            default => new JsonResponse(['error' => 'Action non reconnue'], 400),
        };
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

    private function updateTalkPosition(Request $request): JsonResponse
    {
        $talkId = $request->request->getInt('talk_id');
        $position = $request->request->get('position');

        $talk = $this->talkRepository->get($talkId);
        if (!$talk) {
            return new JsonResponse(['error' => 'Conférence non trouvée'], 404);
        }

        $talk->setPosition($position === null || $position === '' ? null : (int) $position);
        $this->talkRepository->save($talk);

        return new JsonResponse(['success' => true]);
    }
}
