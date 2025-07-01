<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\SpeakerInfos\Form\HotelReservationType;
use AppBundle\SpeakerInfos\Form\SpeakersContactType;
use AppBundle\SpeakerInfos\Form\SpeakersDinerType;
use AppBundle\SpeakerInfos\Form\SpeakersExpensesType;
use AppBundle\SpeakerInfos\Form\TravelSponsorType;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SpeakerPage extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly SpeakersExpensesStorage $speakersExpensesStorage,
    ) {}

    public function handleRequest(Request $request, Event $event, Speaker $speaker)
    {
        $talks = array_filter(
            iterator_to_array($this->talkRepository->getTalksBySpeaker($event, $speaker)),
            static fn(Talk $talk): bool => $talk->getScheduled(),
        );

        $now = new DateTime('now');

        $speakersContactDefaults = [
            'phone_number' => $speaker->getPhoneNumber(),
        ];
        $speakersContactType = $this->createForm(SpeakersContactType::class, $speakersContactDefaults);
        $speakersContactType->handleRequest($request);
        if ($speakersContactType->isSubmitted() && $speakersContactType->isValid()) {
            $speakersContactData = $speakersContactType->getData();
            $speaker->setPhoneNumber($speakersContactData['phone_number']);
            $this->speakerRepository->save($speaker);
            $this->addFlash('notice', 'Informations de contact enregistrées');

            return $this->redirectFromRequest($request);
        }

        $speakersDinerDefaults = [
            'will_attend' => $speaker->getWillAttendSpeakersDiner(),
            'has_special_diet' => $speaker->getHasSpecialDiet(),
            'special_diet_description' => $speaker->getSpecialDietDescription(),
        ];
        $speakersDinerType = $this->createForm(SpeakersDinerType::class, $speakersDinerDefaults);
        $speakersDinerType->handleRequest($request);

        $shouldDisplaySpeakersDinerForm = $event->getSpeakersDinerEnabled() && $event->getDateEndSpeakersDinerInfosCollection() > $now;

        if ($shouldDisplaySpeakersDinerForm && $speakersDinerType->isSubmitted() && $speakersDinerType->isValid()) {
            $speakersDinerData = $speakersDinerType->getData();
            $speaker->setWillAttendSpeakersDiner($speakersDinerData['will_attend'] === 1);
            $speaker->setHasSpecialDiet($speakersDinerData['has_special_diet'] === 1);
            $speaker->setSpecialDietDescription($speakersDinerData['special_diet_description']);
            $this->speakerRepository->save($speaker);
            $this->addFlash('notice', 'Informations sur votre venue au restaurant des speakers enregistrées');

            return $this->redirectFromRequest($request);
        }

        $nights = $speaker->getHotelNightsArray();

        if ($speaker->hasNoHotelNight()) {
            $nights[] = HotelReservationType::NIGHT_NONE;
        }

        if ($speaker->hasHostingSponsor()) {
            $nights[] = HotelReservationType::NIGHT_TRAVEL_SPONSOR;
        }

        $hotelReservationDefaults = [
            'nights' => $nights,
        ];

        $hotelReservationType = $this->createForm(HotelReservationType::class, $hotelReservationDefaults, ['event' => $event]);
        $hotelReservationType->handleRequest($request);

        $shouldDisplayHotelReservationForm = $event->getAccomodationEnabled() && $event->getDateEndHotelInfosCollection() > $now;

        if ($shouldDisplayHotelReservationForm && $hotelReservationType->isSubmitted() && $hotelReservationType->isValid()) {
            $hotelReservationData = $hotelReservationType->getData();
            if (is_array($hotelReservationData['nights'])) {
                $hasHostingSponsor = false;

                $nights = array_filter($hotelReservationData['nights'], function (mixed $value) use (&$hasHostingSponsor) {
                    if ($value === HotelReservationType::NIGHT_TRAVEL_SPONSOR) {
                        $hasHostingSponsor = true;

                        return false;
                    }

                    if ($value === HotelReservationType::NIGHT_NONE) {
                        return false;
                    }

                    return true;
                });

                $speaker->setHasHostingSponsor($hasHostingSponsor);
                $speaker->setHotelNightsArray($nights);
            }

            $this->speakerRepository->save($speaker);
            $this->addFlash('notice', "Informations sur votre venue à l'hôtel enregistrées");

            return $this->redirectFromRequest($request);
        }

        $speakersExpensesType = $this->createForm(SpeakersExpensesType::class);
        $speakersExpensesType->handleRequest($request);
        if ($speakersExpensesType->isSubmitted() && $speakersExpensesType->isValid()) {
            $speakersExpensesData = $speakersExpensesType->getData();
            foreach ($speakersExpensesData['files'] as $file) {
                $this->speakersExpensesStorage->store($file, $speaker);
            }
            $this->addFlash('notice', 'Fichiers ajoutés');

            return $this->redirectFromRequest($request);
        }

        $travelSponsorType = $this->createForm(TravelSponsorType::class, TravelSponsorType::buildDefaultFromSpeaker($speaker));
        $travelSponsorType->handleRequest($request);
        if ($travelSponsorType->isSubmitted() && $travelSponsorType->isValid()) {
            $travelSponsorData = $travelSponsorType->getData();

            $speaker->setTravelRefundNeeded(!in_array(TravelSponsorType::OPTION_NOT_NEEDED, $travelSponsorData['choices'], true));
            $speaker->setTravelRefundSponsored(in_array(TravelSponsorType::OPTION_SPONSORED, $travelSponsorData['choices'], true));

            $this->speakerRepository->save($speaker);
            $this->addFlash('notice', "Informations sur vos transports enregistrées");

            return $this->redirectFromRequest($request);
        }

        if ($request->query->has('delete_file')) {
            $this->speakersExpensesStorage->delete($request->query->get('delete_file'), $speaker);
            $request->query->remove('delete_file');

            return $this->redirectFromRequest($request);
        }

        $description = '';
        $eventCfp = $event->getCFP();
        if (isset($eventCfp['speaker_management_en']) && $request->getLocale() === 'en') {
            $description = $eventCfp['speaker_management_en'];
        } elseif (isset($eventCfp['speaker_management_fr'])) {
            $description = $eventCfp['speaker_management_fr'];
        }

        return $this->render('event/speaker/page.html.twig', [
            'event' => $event,
            'description' => $description,
            'talks_infos' => $this->addTalkInfos($event, $talks),
            'speaker' => $speaker,
            'should_display_speakers_diner_form' => $shouldDisplaySpeakersDinerForm,
            'should_display_hotel_reservation_form' => $shouldDisplayHotelReservationForm,
            'speakers_expenses_form' => $speakersExpensesType->createView(),
            'speakers_expenses_files' => $this->speakersExpensesStorage->getFiles($speaker),
            'travel_sponsor_form' => $travelSponsorType->createView(),
            'speakers_diner_form' => $speakersDinerType->createView(),
            'hotel_reservation_form' => $hotelReservationType->createView(),
            'speakers_contact_form' => $speakersContactType->createView(),
            'day_before_event' => DateTimeImmutable::createFromMutable($event->getDateStart())->modify('- 1 day'),
        ]);
    }

    /**
     * @param Talk[] $talks
     *
     * @return array<array{talk: Talk, room: ?Room, planning: ?Planning}>
     */
    protected function addTalkInfos(Event $event, array $talks): array
    {
        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event, false);
        $allTalksById = [];
        foreach ($talkAggregates as $talkAggregate) {
            $allTalksById[$talkAggregate->talk->getId()] = [
                'talk' => $talkAggregate->talk,
                'room' => $talkAggregate->room,
                'planning' => $talkAggregate->planning,
            ];
        }

        $speakerTalks = [];
        foreach ($talks as $talk) {
            if (!isset($allTalksById[$talk->getId()])) {
                continue;
            }
            $speakerTalks[] = $allTalksById[$talk->getId()];
        }

        return $speakerTalks;
    }

    private function redirectFromRequest(Request $request): RedirectResponse
    {
        $params = array_merge($request->attributes->get('_route_params'), $request->query->all());
        return $this->redirectToRoute($request->attributes->get('_route'), $params);
    }
}
