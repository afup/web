<?php

declare(strict_types=1);

namespace AppBundle\Event;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TicketEventType;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use Symfony\Component\Asset\Packages;

class JsonLd
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
        private readonly TicketTypeAvailability $ticketTypeAvailability,
        private readonly Packages $packages,
        private readonly PhotoStorage $photoStorage,
    ) {
    }

    public function getDataForEvent(Event $event): array
    {
        /**
         * @var Talk[] $talks
         */
        $talks = $this->talkRepository->getByEventWithSpeakers($event);

        $subEvents = [];
        foreach ($talks as $talkInfo) {
            $performers = [];
            foreach ($talkInfo['.aggregation']['speaker'] as $speaker) {
                /**
                 * @var Speaker $speaker
                 */
                $url = $this->photoStorage->getUrl($speaker);
                $performers[] = [
                    '@type' => 'Person',
                    'name' => $speaker->getLabel(),
                    'image' => $url ? $this->packages->getUrl($url) : '',
                    'url' => $speaker->getTwitter() !== null ? 'https://twitter.com/' . $speaker->getTwitter() : null,
                ];
            }

            $subEvent = [
                '@type' => 'Event',
                'name' => $talkInfo['talk']->getTitle(),
                'description' => html_entity_decode(strip_tags((string) $talkInfo['talk']->getDescription())),
                'location' => [
                    '@type' => 'Place',
                    'name' => $talkInfo['room'] ? $talkInfo['room']->getName() : '',
                    'address' => $event->getPlaceAddress(),
                ],
                'performers' => $performers,

            ];

            if ($talkInfo['planning'] && $event->isPlanningDisplayable()) {
                $subEvent['startDate'] = $talkInfo['planning']->getStart()->format('c');
                $subEvent['endDate'] = $talkInfo['planning']->getEnd()->format('c');
            }

            $subEvents[] = $subEvent;
        }

        $offers = [];
        /**
         * @var TicketEventType[] $eventTickets
         */
        $eventTickets = $this->ticketEventTypeRepository->getTicketsByEvent($event, true, TicketEventTypeRepository::ACTUAL_TICKETS_ONLY);

        $available = [
            '@type' => 'ItemAvailability',
            'name' => 'In stock',
        ];
        $notAvailable = [
            '@type' => 'ItemAvailability',
            'name' => 'Out of stock',
        ];

        foreach ($eventTickets as $eventTicket) {
            $offers[] = [
                '@type' => 'Offer',
                'name' => $eventTicket->getTicketType()->getPrettyName(),
                'sku' => $eventTicket->getTicketType()->getTechnicalName(),
                'priceCurrency' => 'EUR',
                'price' => $eventTicket->getPrice(),
                'validFrom' => $eventTicket->getDateStart()->format('c'),
                'validThrough' => $eventTicket->getDateEnd()->format('c'),
                'availability' => $this->ticketTypeAvailability->getStock($eventTicket, $event) > 0 ? $available : $notAvailable,
            ];
        }

        return [
            "@context" => "http://schema.org",
            "@type" => "BusinessEvent",
            "name" => $event->getTitle(),
            'description' => $this->getDescription($event),
            "url"=> "https://event.afup.org",
            'location' => [
                "@type" => "Place",
                "name" => $event->getPlaceName(),
                'address' => $event->getPlaceAddress(),
            ],
            'isAccessibleForFree' => false,
            'organizer' => [
                '@type' => 'Organization',
                'name' => 'AFUP',
                'logo' => 'https://afup.org/uploads/speakers/17/thumbnails/1754.png', // @todo do not depend of "uploads" folder
                'url' => 'https://afup.org',
            ],
            'startDate' => $event->getDateStart()->format('c'),
            'endDate' => $event->getDateEnd()->format('c'),
            'offers' => $offers,
            'image' => 'https://afup.org//templates/site/images/logoFPHP2017-420x207.png',
            'subEvents' => $subEvents,
        ];
    }

    private function getDescription(Event $event): string
    {
        if ($event->getDateStart()->format('Y-m-d') === $event->getDateEnd()->format('Y-m-d')) {
            return sprintf(
                'Le %s aura lieu le %s %s au %s.',
                $event->getTitle(),
                $event->getDateStart()->format('d'),
                $event->getDateEnd()->format('M'),
                $event->getPlaceName()
            );
        }

        return sprintf(
            'Le %s aura lieu les %s et %s %s au %s.',
            $event->getTitle(),
            $event->getDateStart()->format('d'),
            $event->getDateEnd()->format('d'),
            $event->getDateEnd()->format('M'),
            $event->getPlaceName()
        );
    }
}
