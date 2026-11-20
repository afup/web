<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Talk\PublishedTalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Event\Talk\VideoObjectJsonLd;
use AppBundle\Event\Talk\RelatedTalkRepository;
use AppBundle\Event\Talk\RelatedTalks;
use AppBundle\Joindin\JoindinComments;
use AppBundle\Subtitles\Parser;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        private readonly PublishedTalkRepository $publishedTalkRepository,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
        private readonly RelatedTalkRepository $relatedTalkRepository,
    ) {}

    public function __invoke(int $id, string $slug): Response
    {
        $talk = $this->talkRepository->get($id);

        if (null === $talk || !$this->publishedTalkRepository->isPublished($id)) {
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
        // Slug de la page de chaque speaker, par ID de ligne speaker : un seul index, chargé une fois pour tous.
        $speakerSlugs = [];
        /** @var Speaker $speaker */
        foreach ($speakers as $speaker) {
            $identity = $this->speakerProfileResolver->getIdentityForSpeaker($speaker);
            if (null !== $identity && null !== $speaker->getId()) {
                $speakerSlugs[$speaker->getId()] = $identity->slug;
            }
        }
        $event = $this->eventRepository->get($planning->eventId);
        if ($event === null) {
            throw $this->createNotFoundException();
        }
        $relatedTalks = RelatedTalks::build(
            $talk,
            $this->relatedTalkRepository->getPublishedByEventInPlanningOrder($event),
            $speakers,
            $this->relatedTalkRepository->getPublishedBySpeakers($speakers, $talk),
        );
        $comments = $this->joindinComments->getCommentsFromTalk($talk);

        $parser = new Parser();
        $parsedContent = $parser->parse($talk->getTranscript());

        return $this->view->render('site/talks/show.html.twig', [
            'talk' => $talk,
            'event' => $event,
            'speakers' => $speakers,
            'speaker_slugs' => $speakerSlugs,
            'comments' => $comments,
            'transcript' => $parsedContent,
            'video_object' => $this->videoObjectJsonLd->getData($talk, $planning->start, $parsedContent, $this->generateUrl('talks_show', ['id' => $talk->getId(), 'slug' => $talk->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL)),
            'related_talks' => $relatedTalks,
        ]);
    }
}
