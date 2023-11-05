<?php

namespace AppBundle\Indexation\Meetups;

use AlgoliaSearch\Client;
use AppBundle\Event\Model\Meetup;
use AppBundle\Event\Model\Repository\MeetupRepository;
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

    public function __construct(Client $algoliaClient, MeetupRepository $meetupRepository)
    {
        $this->algoliaClient = $algoliaClient;
        $this->meetupRepository = $meetupRepository;
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

        return $this->fromCollectionInterfaceToArray($meetupsCollection);
    }

    /**
     * @param CollectionInterface $meetupsCollection
     * @return array<Meetup>
     */
    public function fromCollectionInterfaceToArray($meetupsCollection)
    {
        $meetupsArray = [];
        foreach ($meetupsCollection as $meetup) {
            $meetupsArray[] = $meetup;
        }
        return $meetupsArray;
    }
}
