<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Talks;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use AppBundle\Event\Entity\Planning;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;

class Runner
{
    private readonly Transformer $transformer;

    public function __construct(
        private readonly SearchClient $algoliaClient,
        private readonly PlanningRepository $planningRepository,
        private readonly TalkRepository $talkRepository,
        private readonly EventRepository $eventRepository,
        private readonly SpeakerRepository $speakerRepository,
    ) {
        $this->transformer = new Transformer();
    }

    public function run(): void
    {
        $index = $this->initIndex();

        $objects = [];

        foreach ($this->getAllPlannings() as $planning) {
            if (null === ($object = $this->prepareObject($planning))) {
                continue;
            }

            $objects[] = $object;
        }

        $index->clearObjects();
        $index->saveObjects($objects, [
            'objectIDKey' => 'planning_id',
        ]);
    }

    protected function initIndex(): SearchIndex
    {
        $index = $this->algoliaClient->initIndex('afup_talks');

        $index->setSettings([
            'attributesForFaceting' => [
                'event.title',
                'speakers.label',
                'has_video',
                'has_slides',
                'video_has_fr_subtitles',
                'video_has_en_subtitles',
                'has_blog_post',
                'type.label',
                'language.label',
            ],
            'customRanking' => [
                "desc(event.start_date)",
                "desc(has_video)",
                "desc(has_blog_post)",
                "desc(has_slides)",
            ],
            'searchableAttributes' => [
                'title',
                'speakers.label',
                'event.title',
            ],
        ]);

        return $index;
    }

    /**
     * @return list<Planning>
     */
    protected function getAllPlannings(): array
    {
        return $this->planningRepository->findAll();
    }

    protected function prepareObject(Planning $planning): ?array
    {
        if ($planning->start > new \DateTime()) {
            return null;
        }

        $talk = $this->talkRepository->get($planning->talkId);

        if (null === $talk || !$talk->isDisplayedOnHistory()) {
            return  null;
        }

        $event = $this->eventRepository->get($planning->eventId);

        if (null === $event) {
            return null;
        }

        $speakers = $this->speakerRepository->getSpeakersByTalk($talk);

        return $this->transformer->transform($planning, $talk, $event, $speakers);
    }
}
