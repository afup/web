<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Talk\RelatedTalks;
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

        if (null === $talk || !$talk->isDisplayedOnHistory()) {
            throw $this->createNotFoundException();
        }

        $planning = $this->planningRepository->getByTalk($talk);
        if ($planning === null) {
            throw $this->createNotFoundException();
        }

        if ($talk->getSlug() !== $slug) {
            return $this->redirectToRoute('talks_show', ['id' => $id, 'slug' => $talk->getSlug()], Response::HTTP_MOVED_PERMANENTLY);
        }

        $speakers = iterator_to_array($this->speakerRepository->getSpeakersByTalk($talk), false);
        $event = $this->eventRepository->get($planning->eventId);
        if ($event === null) {
            throw $this->createNotFoundException();
        }
        $relatedTalks = RelatedTalks::build(
            $talk,
            $this->talkRepository->getPublishedByEventInPlanningOrder($event),
            $speakers,
            $this->talkRepository->getPublishedBySpeakers($speakers, $talk),
        );
        $comments = $this->joindinComments->getCommentsFromTalk($talk);

        $parser = new Parser();
        $parsedContent = $parser->parse($talk->getTranscript());

        return $this->view->render('site/talks/show.html.twig', [
            'talk' => $talk,
            'event' => $event,
            'speakers' => $speakers,
            'comments' => $comments,
            'transcript' => $parsedContent,
            'related_talks' => $relatedTalks,
        ]);
    }
}
