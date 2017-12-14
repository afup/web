<?php

namespace AppBundle\Indexation\Meetups;

use AppBundle\Offices\OfficesCollection;

class Transformer
{
    /**
     * @var OfficesCollection
     */
    private $officesCollection;

    /**
     * @param OfficesCollection $officesCollection
     */
    public function __construct(OfficesCollection $officesCollection)
    {
        $this->officesCollection = $officesCollection;
    }

    /**
     * @param array $meetup
     *
     * @return array
     */
    public function transform(array $meetup)
    {
        $office = $this->officesCollection->findByMeetupId($meetup['group']['id']);

        $datetime = new \DateTime('@' . ($meetup['time'] / 1000));

        $isUpcoming = $meetup['status'] == 'upcoming';

        $item = [
            'meetup_id' => $meetup['id'],
            'label' => $meetup['name'],
            'event_url' => $meetup['event_url'],
            'timestamp' => $datetime->format('U'),
            'year' => $datetime->format('Y'),
            'datetime' => $datetime->format('Y-m-d H:i:s'),
            'day_month' => $datetime->format('d M'),
            'office' => [
                'label' => $office['label'],
                'logo_url' => $office['logo_url'],
            ],
            'description' => $meetup['description'],
            'is_upcoming' => $isUpcoming,
            'custom_sort' => $isUpcoming ? PHP_INT_MAX - $meetup['time'] : $meetup['time'],
        ];

        if (isset($meetup['venue'])) {
            $item['venue'] = [
                'name' => $meetup['venue']['name'],
                'address_1' => $meetup['venue']['address_1'],
                'city' => $meetup['venue']['city'],
            ];
        }

        return $item;
    }
}
