<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Event\Model\Repository\TalkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TalkWidgetAction extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $talks = $this->talkRepository->getBy(['id' => explode(',', (string) $request->get('ids'))]);

        $speakers = [];
        $talksInfos = [];
        foreach ($talks as $talk) {
            foreach ($this->talkRepository->getByTalkWithSpeakers($talk) as $row) {
                $talksInfos[] = $row;
                foreach ($row['.aggregation']['speaker'] as $speaker) {
                    $speakers[$speaker->getId()] = $speaker;
                }
            }
        }

        return $this->render(
            'blog/talk.html.twig',
            [
                'talks_infos' => $talksInfos,
                'speakers' => $speakers,
                'widget_type' => $request->get('type', 'all'),
            ],
        );
    }
}
