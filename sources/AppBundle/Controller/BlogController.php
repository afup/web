<?php


namespace AppBundle\Controller;

use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\TicketEventType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends EventBaseController
{
    public function testJsonAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);
        $photoStorage = $this->get('app.photo_storage');
        $packages = $this->get('assets.packages');
        /**
         * @var TalkRepository $talkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        /**
         * @var $talks Talk[]
         */
        $talks = $talkRepository->getByEventWithSpeakers($event);

        $ticketTypeAvailability = $this->get('app.ticket_availability');

        $subEvents = [];
        foreach ($talks as $talkInfo) {
            $performers = [];
            foreach ($talkInfo['.aggregation']['speaker'] as $speaker) {
                /**
                 * @var Speaker $speaker
                 */
                $performers[] = [
                    '@type' => 'Person',
                    'name' => $speaker->getLabel(),
                    'image' => $packages->getUrl($photoStorage->getUrl($speaker)),
                    'url' => $speaker->getTwitter() !== null ? 'https://twitter.com/' . $speaker->getTwitter() : null
                ];
            }

            $subEvents[] = [
                '@type' => 'Event',
                'name' => $talkInfo['talk']->getTitle(),
                'description' => $talkInfo['talk']->getDescription(),
                'location' => [
                    '@type' => 'Place',
                    'name' => $talkInfo['room']->getName(),
                    'address' => '17 Boulevard Saint-Jacques, 75014 Paris' // @todo do better
                ],
                'performers' => $performers,
                'startDate' => $talkInfo['planning']->getStart()->format('c'),
                'endDate' => $talkInfo['planning']->getEnd()->format('c')
            ];
        }

        $offers = [];
        /**
         * @var $eventTickets TicketEventType[]
         */
        $eventTickets = $this->get('ting')->get(TicketEventTypeRepository::class)->getTicketsByEvent($event);

        $available = [
            '@type' => 'ItemAvailability',
            'name' => 'In stock'
        ];
        $notAvailable = [
            '@type' => 'ItemAvailability',
            'name' => 'Out of stock'
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
                'availability' => $ticketTypeAvailability->getStock($eventTicket, $event) > 0 ? $available : $notAvailable
            ];
        }

        $data = [
            "@context" => "http://schema.org",
            "@type" => "BusinessEvent",
            "name" => $event->getTitle(),
            'description' => 'Description', // @todo record in db
            "url"=> "https://event.afup.org",
            'location' => [
                "@type" => "Place",
                "name" => "Marriott Paris Rive Gauche Hotel & Conference Center",
                'address' => '17 Boulevard Saint-Jacques, 75014 Paris'// @todo record this in db
            ],
            'isAccessibleForFree' => false,
            'organizer' => [
                '@type' => 'Organization',
                'name' => 'AFUP',
                'logo' => 'https://afup.org/uploads/speakers/17/thumbnails/1754.png', // @todo do not depend of "uploads" folder
                'url' => 'https://afup.org'
            ],
            'startDate' => $event->getDateStart()->format('c'),
            'endDate' => $event->getDateEnd()->format('c'),
            'offers' => $offers,
            'image' => 'https://afup.org//templates/site/images/logoFPHP2017-420x207.png',
            'subEvents' => $subEvents
        ];

        return new JsonResponse($data);
    }

    /**
     * @param $eventSlug
     * @return Response
     */
    public function programAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        /**
         * @var $talkRepository TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        $talks = $talkRepository->getByEventWithSpeakers($event);

        return $this->render(':blog:program.html.twig', ['talks' => $talks, 'event' => $event]);
    }
    /**
     * @param $eventSlug
     * @return Response
     */
    public function planningAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        /**
         * @var $talkRepository TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        $talks = $talkRepository->getByEventWithSpeakers($event);

        $eventPlanning = [];
        $rooms = [];

        foreach ($talks as $talkWithData) {
            /**
             * @var $talk Talk
             */
            $talk = $talkWithData['talk'];

            /**
             * @var $planning Planning
             */
            $planning = $talkWithData['planning'];

            /**
             * @var $room Room
             */
            $room = $talkWithData['room'];

            if ($planning === null) {
                continue;
            }

            $startDay = $planning->getStart()->format('d/m/Y');
            if (isset($eventPlanning[$startDay]) === false) {
                $eventPlanning[$startDay] = [];
            }

            $start = $planning->getStart()->setTimezone(new \DateTimeZone('Europe/Paris'))->format('d/m/Y H:i');

            if (isset($eventPlanning[$startDay][$start])=== false) {
                $eventPlanning[$startDay][$start] = [];
            }

            $interval = $planning->getEnd()->diff($planning->getStart());
            $talkWithData['length'] = $interval->i + $interval->h * 60;

            $eventPlanning[$startDay][$start][$room->getId()] = $talkWithData;

            if (isset($rooms[$room->getId()]) === false) {
                $rooms[$room->getId()] = $room;
            }
        }

        return $this->render(
            ':blog:planning.html.twig',
                [
                    'planning' => $eventPlanning,
                    'event' => $event,
                    'rooms' => $rooms,
                    'hourMin' => 8,
                    'hourMax' => 17,
                    'precision' => 5
                ]
        );
    }

    /**
     * @param $eventSlug
     * @return Response
     */
    public function speakersAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        /**
         * @var $speakerRepository SpeakerRepository
         */
        $speakerRepository = $this->get('ting')->get(SpeakerRepository::class);
        $speakers = $speakerRepository->getScheduledSpeakersByEvent($event);

        return $this->render(':blog:speakers.html.twig', ['speakers' => $speakers, 'event' => $event]);
    }
}
