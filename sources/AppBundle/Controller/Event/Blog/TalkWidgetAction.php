<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Event\Entity\Repository\InterviewRepository;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TalkWidgetAction extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly InterviewRepository $interviewRepository,
        private readonly PlanningRepository $planningRepository,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
        private readonly SpeakerRepository $speakerRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $widgetType = $request->query->get('type', 'all');
        $talks = $this->talkRepository->getBy(['id' => explode(',', (string) $request->query->get('ids'))]);

        $talkIds = array_map(static fn($talk): int => (int) $talk->getId(), iterator_to_array($talks, false));
        $plannings = $this->planningRepository->findIndexedByTalkIds($talkIds);
        $rooms = $this->planningRepository->findRoomsOf($plannings);
        $speakersByTalk = $this->speakerRepository->findIndexedByTalkIds($talkIds);

        $event = null;
        $speakers = [];
        $talksInfos = [];
        foreach ($talks as $talk) {
            $row = $this->talkRepository->getPublishedScheduledWithEvent($talk);
            if ($row === null) {
                continue;
            }

            // Les conférenciers, les salles et les plannings ne sont plus hydratés par Ting : ils sont
            // rechargés via Doctrine.
            $talkId = (int) $talk->getId();
            $row['planning'] = $plannings[$talkId] ?? null;
            $row['room'] = $row['planning']?->roomId !== null ? ($rooms[$row['planning']->roomId] ?? null) : null;
            if ($row['event'] instanceof Event) {
                $event = $row['event'];
            }

            $talksInfos[] = $row;
            foreach ($speakersByTalk[$talkId] ?? [] as $speaker) {
                $speakers[$speaker->id] = $speaker;
            }
        }

        // Slug de la page speaker sur afup.org, pour les seules personnes qui ont un talk publié
        // (sinon la page n'existe pas) : un seul index, chargé une fois pour tous.
        $speakerSlugs = [];
        foreach ($speakers as $speakerId => $speaker) {
            $identity = $this->speakerProfileResolver->getIdentityForSpeaker($speaker);
            if (null !== $identity) {
                $speakerSlugs[$speakerId] = $identity->slug;
            }
        }

        $data = [
            'talks_infos' => $talksInfos,
            'speakers' => $speakers,
            'speaker_slugs' => $speakerSlugs,
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
