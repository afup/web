<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Talk\VideoObjectJsonLd;
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
        private readonly VideoObjectJsonLd $videoObjectJsonLd,
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

        $speakers = $this->speakerRepository->getSpeakersByTalk($talk);
        $event = $this->eventRepository->get($planning->eventId);
        $comments = $this->joindinComments->getCommentsFromTalk($talk);

        $parser = new Parser();
        $parsedContent = $parser->parse($talk->getTranscript());

        return $this->view->render('site/talks/show.html.twig', [
            'talk' => $talk,
            'event' => $event,
            'speakers' => $speakers,
            'comments' => $comments,
            'transcript' => $parsedContent,
            'video_object' => $this->videoObjectJsonLd->getData($talk, $planning->start, $parsedContent),
        ]);
    }
}
