<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Event\Entity\Repository\InterviewRepository;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TalkWidgetAction extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly InterviewRepository $interviewRepository,
        private readonly PlanningRepository $planningRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $widgetType = $request->query->get('type', 'all');
        $talks = $this->talkRepository->getBy(['id' => explode(',', (string) $request->query->get('ids'))]);

        $talkIds = array_map(static fn($talk) => (int) $talk->getId(), iterator_to_array($talks, false));
        $plannings = $this->planningRepository->findIndexedByTalkIds($talkIds);

        $event = null;
        $speakers = [];
        $talksInfos = [];
        foreach ($talks as $talk) {
            foreach ($this->talkRepository->getByTalkWithSpeakers($talk) as $row) {
                // Les plannings ne sont plus hydratés par Ting : ils sont rechargés
                // via Doctrine avec PlanningRepository::findIndexedByTalkIds().
                $row['planning'] = $talk->getId() !== null ? ($plannings[$talk->getId()] ?? null) : null;
                if (($row['event'] ?? null) instanceof Event) {
                    $event = $row['event'];
                }

                $talksInfos[] = $row;
                /** @var Speaker $speaker */
                foreach ($row['.aggregation']['speaker'] as $speaker) {
                    $speakers[(int) $speaker->getId()] = $speaker;
                }
            }
        }

        $data = [
            'talks_infos' => $talksInfos,
            'speakers' => $speakers,
            'widget_type' => $widgetType,
            'event' => $event,
            'questions' => [],
        ];

        if ($widgetType === 'full' && count($speakers) > 0) {
            $data['questions'] = $this->interviewRepository
                ->findOneBySpeakerId(array_key_first($speakers))
                ->questions ?? [];
        }

        return $this->render('blog/talk.html.twig', $data);
    }
}
