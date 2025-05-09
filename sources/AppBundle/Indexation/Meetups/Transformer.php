<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups;

use AppBundle\Antennes\Antenne;
use AppBundle\Antennes\AntennesCollection;
use AppBundle\Event\Model\Meetup;

class Transformer
{
    const MEETUP_URL = 'https://www.meetup.com/fr-FR/';

    public function __construct(private readonly AntennesCollection $antennesCollection)
    {
    }

    /**
     * @return array
     */
    public function transform(Meetup $meetup): array
    {
        $codeOffice = $meetup->getAntenneName();
        $antenne = $this->antennesCollection->findByCode($codeOffice);
        $datetime = $meetup->getDate();

        $isUpcoming = new \DateTime() < $datetime;

        $eventUrl = $this->getEventUrl($antenne, $meetup);
        $item = [
            'meetup_id' => $meetup->getId(),
            'label' => $meetup->getTitle(),
            'event_url' => $eventUrl,
            'timestamp' => $datetime->format('U'),
            'year' => $datetime->format('Y'),
            'datetime' => $datetime->format('Y-m-d H:i:s'),
            'day_month' => $datetime->format('d M'),
            'office' => [
                'label' => $antenne->label,
                'logo_url' => $antenne->logoUrl,
            ],
            'description' => $meetup->getDescription(),
            'is_upcoming' => $isUpcoming,
            'custom_sort' => $isUpcoming ? PHP_INT_MAX - $meetup->getDate()->getTimestamp() : $meetup->getDate()->getTimestamp(),
        ];

        if ($antenne->socials->twitter !== null) {
            $item['twitter'] = $antenne->socials->twitter;
        }

        return $item;
    }

    private function getEventUrl(Antenne $antenne, Meetup $meetup): string
    {
        return self::MEETUP_URL . $antenne->meetup->urlName . '/events/' . $meetup->getId();
    }
}
