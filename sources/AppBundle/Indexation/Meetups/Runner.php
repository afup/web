<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use AppBundle\Antennes\AntennesCollection;
use AppBundle\Event\Model\Meetup;
use AppBundle\Event\Model\Repository\MeetupRepository;
use CCMBenchmark\Ting\Repository\CollectionInterface;

class Runner
{
    protected SearchClient $algoliaClient;

    protected MeetupRepository $meetupRepository;

    protected Transformer $transformer;

    public function __construct(SearchClient $algoliaClient, MeetupRepository $meetupRepository)
    {
        $this->algoliaClient = $algoliaClient;
        $this->meetupRepository = $meetupRepository;
        $this->transformer = new Transformer(new AntennesCollection());
    }

    public function run(): void
    {
        $index = $this->initIndex();

        echo "Indexation des meetups en cours ...\n\n";

        $meetups = $this->getTransformedMeetupsFromDatabase();

        $index->clearObjects();
        $index->saveObjects($meetups, [
            'objectIDKey' => 'meetup_id'
        ]);

        echo "Indexation des meetups terminée avec succès !\n";
    }

    protected function initIndex(): SearchIndex
    {
        $index = $this->algoliaClient->initIndex('afup_meetups');

        $index->setSettings([
            'attributesForFaceting' => [
                'office.label',
                'year',
                'is_upcoming',
            ],
            'customRanking' => [
                "desc(custom_sort)",
            ],
            'searchableAttributes' => [
                'label',
                'office.label',
                'description',
            ],
        ]);

        return $index;
    }

    private function getTransformedMeetupsFromDatabase(): array
    {
        $meetupsCollection = $this->meetupRepository->getAll();

        return $this->transformMeetupsForIndexation($meetupsCollection);
    }

    /**
     * @param CollectionInterface<Meetup> $meetupsCollection
     * @return list<array>
     */
    public function transformMeetupsForIndexation(CollectionInterface $meetupsCollection): array
    {
        $meetupsArray = [];
        /** @var Meetup $meetup */
        foreach ($meetupsCollection as $meetup) {
            if (null === ($transformedMeetup = $this->transformer->transform($meetup))) {
                continue;
            }
            $meetupsArray[] = $transformedMeetup;
        }

        return $meetupsArray;
    }
}
