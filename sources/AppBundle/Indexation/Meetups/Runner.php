<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups;

use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use AlgoliaSearch\Index;
use AppBundle\Event\Model\Meetup;
use AppBundle\Event\Model\Repository\MeetupRepository;
use AppBundle\Offices\OfficesCollection;
use CCMBenchmark\Ting\Repository\CollectionInterface;

class Runner
{
    protected Client $algoliaClient;

    protected MeetupRepository $meetupRepository;

    protected OfficesCollection $officiesCollection;

    protected Transformer $transformer;

    public function __construct(Client $algoliaClient, MeetupRepository $meetupRepository)
    {
        $this->algoliaClient = $algoliaClient;
        $this->meetupRepository = $meetupRepository;
        $this->officiesCollection = new OfficesCollection();
        $this->transformer = new Transformer($this->officiesCollection);
    }

    /**
     *
     * @throws AlgoliaException
     */
    public function run(): void
    {
        $index = $this->initIndex();

        echo "Indexation des meetups en cours ...\n\n";

        $meetups = $this->getTransformedMeetupsFromDatabase();

        $index->clearIndex();
        $index->addObjects($meetups, 'meetup_id');

        echo "Indexation des meetups terminée avec succès !\n";
    }

    /**
     * @return Index
     * @throws AlgoliaException
     */
    protected function initIndex()
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
     * @param CollectionInterface $meetupsCollection
     * @return array<Meetup>
     */
    public function transformMeetupsForIndexation($meetupsCollection): array
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
