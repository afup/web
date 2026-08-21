<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups;

use AppBundle\Antennes\Antenne;
use AppBundle\Antennes\AntenneRepository;
use AppBundle\Event\Entity\Meetup;

class Transformer
{
    public const string MEETUP_URL = 'https://www.meetup.com/fr-FR/';

    public function __construct(private readonly AntenneRepository $antennesCollection) {}

    /**
     * @return array
     */
    public function transform(Meetup $meetup): array
    {
        $codeOffice = $meetup->codeAntenne;
        $antenne = $this->antennesCollection->findByCode($codeOffice);
        $datetime = $meetup->date;

        $isUpcoming = new \DateTime() < $datetime;

        $eventUrl = $this->getEventUrl($antenne, $meetup);

        $parseDown = new \Parsedown();

        $item = [
            'meetup_id' => $meetup->id,
            'label' => $meetup->titre,
            'event_url' => $eventUrl,
            'timestamp' => $datetime->format('U'),
            'year' => $datetime->format('Y'),
            'datetime' => $datetime->format('Y-m-d H:i:s'),
            'day_month' => $datetime->format('d M'),
            'office' => [
                'label' => $antenne->label,
                'logo_url' => $antenne->logoUrl,
            ],
            'description' => $parseDown->parse($meetup->description),
            'is_upcoming' => $isUpcoming,
            'custom_sort' => $isUpcoming ? PHP_INT_MAX - $meetup->date->getTimestamp() : $meetup->date->getTimestamp(),
        ];

        if ($antenne->socials->twitter !== null) {
            $item['twitter'] = $antenne->socials->twitter;
        }

        return $item;
    }

    private function getEventUrl(Antenne $antenne, Meetup $meetup): string
    {
        return self::MEETUP_URL . $antenne->meetup->urlName . '/events/' . $meetup->id;
    }
}
