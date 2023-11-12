<?php

namespace AppBundle\Indexation\Meetups;

use AlgoliaSearch\Client;
use AppBundle\Event\Model\Meetup;
use AppBundle\Event\Model\Repository\MeetupRepository;
use AppBundle\Offices\OfficesCollection;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Runner
{
    /**
     * @var Client
     */
    protected $algoliaClient;

    /**
     * @var MeetupRepository
     */
    protected $meetupRepository;

    /**
     * @var OfficesCollection
     */
    protected $officiesCollection;

    /**
     * @var Transformer
     */
    protected $transformer;

    public function __construct(Client $algoliaClient, MeetupRepository $meetupRepository)
    {
        $this->algoliaClient = $algoliaClient;
        $this->meetupRepository = $meetupRepository;
        $this->officiesCollection = new OfficesCollection();
        $this->transformer = new Transformer($this->officiesCollection);
    }

    /**
     *
     */
    public function run()
    {
        $index = $this->initIndex();

        $process = new Process(['php', 'bin/console', 'scraping:meetup:event']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $meetups = $this->getMeetupsFromDatabase();

        $index->clearIndex();
        $index->addObjects($meetups, 'meetup_id');
    }

    /**
     * @return \AlgoliaSearch\Index
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

    /**
     * @return array
     */
    private function getMeetupsFromDatabase()
    {
        $meetupsCollection = $this->meetupRepository->getAll();

        return $this->transformMeetupsForIndexation($meetupsCollection);
    }

    /**
     * @param CollectionInterface $meetupsCollection
     * @return array<Meetup>
     */
    public function transformMeetupsForIndexation($meetupsCollection)
    {
        $meetupsArray = [];
        foreach ($meetupsCollection as $meetup) {
            if (null === ($transformedMeetup = $this->transformer->transform($meetup))) {
                continue;
            }
            $meetupsArray[] = $transformedMeetup;
        }
        return $meetupsArray;
    }
}
