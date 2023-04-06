<?php

namespace AppBundle\Indexation\Meetups;

use AlgoliaSearch\Client;
use AppBundle\Offices\OfficesCollection;
use DMS\Service\Meetup\MeetupOAuthClient;

class Runner
{
    /**
     * @var Client
     */
    protected $algoliaClient;

    /**
     * @var MeetupOAuthClient
     */
    protected $meetupClient;

    /**
     * @var OfficesCollection
     */
    protected $officiesCollection;

    /**
     * @var Transformer
     */
    protected $transformer;

    public function __construct(Client $algoliaClient, MeetupOAuthClient $meetupClient)
    {
        $this->algoliaClient = $algoliaClient;
        $this->meetupClient = $meetupClient;
        $this->officiesCollection = new OfficesCollection();
        $this->transformer = new Transformer($this->officiesCollection);
    }

    /**
     *
     */
    public function run()
    {
        $index = $this->initIndex();

        $command = $this->meetupClient->getCommand(
            'GetEvents',
            [
                'group_id' => implode(',', $this->getGroupIds()),
                'status' => 'upcoming,past',
                'order' => 'time',
                'desc' => 'true',
            ]
        );

        $command->prepare();

        $meetups = [];

        foreach ($command->execute() as $meetup) {
            if (null === ($transformedMeetup = $this->transformer->transform($meetup))) {
                continue;
            }
            $meetups[] = $transformedMeetup;
        }

        $index->clearIndex();
        $index->addObjects($meetups, 'meetup_id');
    }

    protected function getGroupIds()
    {
        $groupIds = [];
        foreach ($this->officiesCollection->getAll() as $office) {
            if (!isset($office['meetup_id'])) {
                continue;
            }
            $groupIds[] = $office['meetup_id'];
        }

        return $groupIds;
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
}
