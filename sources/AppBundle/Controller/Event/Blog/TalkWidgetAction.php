<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Event\Entity\Repository\InterviewRepository;
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
    ) {}

    public function __invoke(Request $request): Response
    {
        $widgetType = $request->query->get('type', 'all');
        $talks = $this->talkRepository->getBy(['id' => explode(',', (string) $request->query->get('ids'))]);

        $speakers = [];
        $talksInfos = [];
        foreach ($talks as $talk) {
            foreach ($this->talkRepository->getByTalkWithSpeakers($talk) as $row) {
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
            'questions' => [],
        ];

        if ($widgetType === 'interview' || $widgetType === 'all' && count($speakers) > 0) {
            $firstSpeakerId = array_key_first($speakers);

            if (is_int($firstSpeakerId)) {
                $data['questions'] = $this->interviewRepository
                    ->findOneBySpeakerId($firstSpeakerId)
                    ->questions ?? [];
            }
        }

        return $this->render('blog/talk.html.twig', $data);
    }
}
