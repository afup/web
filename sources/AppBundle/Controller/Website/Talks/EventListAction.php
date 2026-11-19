<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Talk\EventTalksSummary;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class EventListAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/talks/events.html.twig', [
            'summaries_by_year' => EventTalksSummary::groupByYear($this->talkRepository->getEventTalksSummaries()),
        ]);
    }
}
