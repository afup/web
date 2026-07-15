<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Interview;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Interview;
use AppBundle\Event\Entity\Repository\InterviewRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly InterviewRepository $interviewRepository,
        #[Autowire('%env(WORDPRESS_BASE_URI)%')]
        private readonly string $wordpressBaseUri,
    ) {}

    public function __invoke(AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;

        $speakers = [];
        foreach ($this->speakerRepository->getScheduledSpeakersByEvent($event, true) as $row) {
            $speaker = $row['speaker'] ?? null;
            if (!$speaker instanceof Speaker) {
                continue;
            }

            $speakers[(int) $speaker->getId()] = $speaker;
        }

        $interviews = $this->interviewRepository->findBySpeakerIds(array_keys($speakers));

        $interviewsWithSpeakers = [];
        $speakerIdsWithInterview = [];
        foreach ($interviews as $interview) {
            $interviewsWithSpeakers[] = [
                'interview' => $interview,
                'speakers' => $this->resolveSpeakers($interview, $speakers),
            ];
            $speakerIdsWithInterview = array_merge($speakerIdsWithInterview, $interview->getSpeakerIds());
        }

        usort(
            $interviewsWithSpeakers,
            fn(array $a, array $b) => $this->firstSpeakerLabel($a['speakers']) <=> $this->firstSpeakerLabel($b['speakers']),
        );

        $speakersWithoutInterview = array_values(array_filter(
            $speakers,
            fn(Speaker $speaker) => !in_array($speaker->getId(), $speakerIdsWithInterview, true),
        ));

        return $this->render('admin/event/interview/list.html.twig', [
            'event' => $event,
            'interviews' => $interviewsWithSpeakers,
            'speakersWithoutInterview' => $speakersWithoutInterview,
            'event_select_form' => $eventSelection->selectForm(),
            'now' => new \DateTimeImmutable(),
            'wordpressBaseUri' => $this->wordpressBaseUri,
        ]);
    }

    /**
     * @param array<int, Speaker> $speakers
     *
     * @return list<Speaker>
     */
    private function resolveSpeakers(Interview $interview, array $speakers): array
    {
        $resolved = [];
        foreach ($interview->getSpeakerIds() as $speakerId) {
            if (isset($speakers[$speakerId])) {
                $resolved[] = $speakers[$speakerId];
            }
        }

        usort($resolved, fn(Speaker $a, Speaker $b) => $a->getLabel() <=> $b->getLabel());

        return $resolved;
    }

    /**
     * @param list<Speaker> $speakers
     */
    private function firstSpeakerLabel(array $speakers): string
    {
        return $speakers === [] ? '' : $speakers[0]->getLabel();
    }
}
