<?php

namespace AppBundle\Controller;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\SpeakerInfos\Form\HotelReservationType;
use AppBundle\SpeakerInfos\Form\SpeakersDinerType;
use Symfony\Component\HttpFoundation\Request;

class SpeakerController extends EventBaseController
{
    public function speakerPageAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);
        $speaker = $this->get('app.speaker_factory')->getSpeaker($event);

        return $this->internalSpeakerPageAction($request, $event, $speaker);
    }

    public function internalSpeakerPageAction(Request $request, Event $event, Speaker $speaker)
    {
        /**
         * @var $talkRepository TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        $talks = $talkRepository->getTalksBySpeaker($event, $speaker);

        /**
         * @var SpeakerRepository $speakerRepository
         */
        $speakerRepository = $this->get('ting')->get(SpeakerRepository::class);


        $speakersDinerDefaults = [
            'will_attend' => $speaker->getWillAttendSpeakersDiner(),
            'has_special_diet' => $speaker->getHasSpecialDiet(),
            'special_diet_description' => $speaker->getSpecialDietDescription(),
        ];
        $speakersDinerType = $this->createForm(SpeakersDinerType::class, $speakersDinerDefaults);
        $speakersDinerType->handleRequest($request);

        if ($speakersDinerType->isValid()) {
            $speakersDinerData = $speakersDinerType->getData();

            $speaker->setWillAttendSpeakersDiner($speakersDinerData['will_attend']);
            $speaker->setHasSpecialDiet($speakersDinerData['has_special_diet']);
            $speaker->setSpecialDietDescription($speakersDinerData['special_diet_description']);
            $speakerRepository->save($speaker);

            $this->addFlash('notice', 'Informations sur votre venue au restaurant des speakers enregistrées');

            return $this->redirectToRoute('speaker-infos', ['eventSlug' => $event->getPath()]);
        }

        $nights = $speaker->getHotelNightsArray();

        if ($speaker->hasNoHotelNight()) {
            $nights[] = HotelReservationType::NIGHT_NONE;
        }

        $hotelReservationDefaults = [
            'nights' => $nights,
        ];

        $hotelReservationType = $this->createForm(HotelReservationType::class, $hotelReservationDefaults, ['event' => $event]);
        $hotelReservationType->handleRequest($request);

        if ($hotelReservationType->isValid()) {
            $hotelReservationData = $hotelReservationType->getData();
            $speaker->setHotelNightsArray($hotelReservationData['nights']);

            $speakerRepository->save($speaker);

            $this->addFlash('notice', "Informations sur votre venue à l'hotel enregistrées");

            return $this->redirectToRoute('speaker-infos', ['eventSlug' => $event->getPath()]);
        }

        return $this->render('event/speaker/page.html.twig', [
            'event' => $event,
            'talks' => $talks,
            'speakers_diner_form' => $speakersDinerType->createView(),
            'hotel_reservation_form' => $hotelReservationType->createView(),
        ]);
    }
}
