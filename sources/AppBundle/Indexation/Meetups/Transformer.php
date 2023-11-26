<?php

namespace AppBundle\Indexation\Meetups;

use AppBundle\Event\Model\Meetup;
use AppBundle\Offices\OfficesCollection;
use Exception;

class Transformer
{
    const MEETUP_URL = 'https://www.meetup.com/fr-FR/';

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
     * @param Meetup $meetup
     *
     * @return array
     * @throws Exception
     */
    public function transform(Meetup $meetup)
    {
        $codeOffice = $meetup->getAntenneName();
        $office = $this->officesCollection->findByCode($codeOffice);
        $datetime = $meetup->getDate();

        $isUpcoming = new \DateTime() < $datetime;

        if (isset($office['meetup_filter'])) {
            $matches = [];
            if (!preg_match($office['meetup_filter'], $meetup['name'], $matches)) {
                return null;
            }

            $meetup['name'] = $matches[1];
        }

        $eventUrl = $this->getEventUrl($office, $meetup);
        $item = [
            'meetup_id' => $meetup->getId(),
            'label' => $meetup->getTitle(),
            'event_url' => $eventUrl,
            'timestamp' => $datetime->format('U'),
            'year' => $datetime->format('Y'),
            'datetime' => $datetime->format('Y-m-d H:i:s'),
            'day_month' => $datetime->format('d M'),
            'office' => [
                'label' => $office['label'],
                'logo_url' => $office['logo_url'],
            ],
            'description' => $meetup->getDescription(),
            'is_upcoming' => $isUpcoming,
            'custom_sort' => $isUpcoming ? PHP_INT_MAX - $meetup->getDate()->getTimestamp() : $meetup->getDate()->getTimestamp(),
        ];

        if (isset($office['twitter'])) {
            $item['twitter'] = $office['twitter'];
        }

        return $item;
    }

    /**
     * @param array $office
     * @param Meetup $meetup
     * @return string
     */
    public function getEventUrl($office, Meetup $meetup)
    {
        return self::MEETUP_URL . $office['meetup_urlname'] . '/events/' . $meetup->getId();
    }
}
