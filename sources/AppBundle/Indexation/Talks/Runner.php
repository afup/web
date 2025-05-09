<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Talks;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;

class Runner
{
    protected Transformer $transformer;

    public function __construct(
        protected SearchClient $algoliaClient,
        protected RepositoryFactory $ting,
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
     * @return CollectionInterface<Planning>
     */
    protected function getAllPlannings()
    {
        return $this->ting->get(PlanningRepository::class)->getAll();
    }

    protected function prepareObject(Planning $planning): ?array
    {
        if ($planning->getStart() > new \DateTime()) {
            return null;
        }

        /** @var Talk|null $talk */
        $talk = $this->ting->get(TalkRepository::class)->get($planning->getTalkId());

        if (null === $talk || !$talk->isDisplayedOnHistory()) {
            return  null;
        }

        $event = $this->ting->get(EventRepository::class)->get($planning->getEventId());

        if (null === $event) {
            return null;
        }

        $speakers = $this->ting->get(SpeakerRepository::class)->getSpeakersByTalk($talk);

        return $this->transformer->transform($planning, $talk, $event, $speakers);
    }
}
