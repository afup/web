<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Joindin\JoindinComments;
use AppBundle\Subtitles\Parser;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ShowAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly JoindinComments $joindinComments,
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly PlanningRepository $planningRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(int $id, string $slug): Response
    {
        $talk = $this->talkRepository->get($id);

        if (null === $talk || $talk->getSlug() != $slug || !$talk->isDisplayedOnHistory()) {
            throw $this->createNotFoundException();
        }

        $speakers = $this->speakerRepository->getSpeakersByTalk($talk);
        $planning = $this->planningRepository->getByTalk($talk);
        $event = $this->eventRepository->get($planning->getEventId());
        $comments = $this->joindinComments->getCommentsFromTalk($talk);

        $parser = new Parser();
        $parsedContent = $parser->parse($talk->getTranscript());

        return $this->view->render('site/talks/show.html.twig', [
            'talk' => $talk,
            'event' => $event,
            'speakers' => $speakers,
            'comments' => $comments,
            'transcript' => $parsedContent,
        ]);
    }
}
