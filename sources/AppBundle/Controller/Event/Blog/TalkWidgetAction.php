<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Event\Entity\Repository\InterviewRepository;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
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
    ) {}

    public function __invoke(Request $request): Response
    {
        $widgetType = $request->query->get('type', 'all');
        $talks = $this->talkRepository->getBy(['id' => explode(',', (string) $request->query->get('ids'))]);

        $talkIds = array_map(static fn($talk) => (int) $talk->getId(), iterator_to_array($talks, false));
        $plannings = $this->planningRepository->findIndexedByTalkIds($talkIds);
        $rooms = $this->planningRepository->findRoomsOf($plannings);

        $event = null;
        $speakers = [];
        $talksInfos = [];
        foreach ($talks as $talk) {
            foreach ($this->talkRepository->getByTalkWithSpeakers($talk) as $row) {
                // Les salles et les plannings ne sont plus hydratés par Ting : ils sont
                // rechargés via Doctrine avec PlanningRepository::findIndexedByTalkIds().
                $row['planning'] = $talk->getId() !== null ? ($plannings[$talk->getId()] ?? null) : null;
                $row['room'] = $row['planning']?->roomId !== null ? ($rooms[$row['planning']->roomId] ?? null) : null;
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
