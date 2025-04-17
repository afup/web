<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\SpeakerInfos\Form\HotelReservationType;
use AppBundle\SpeakerInfos\Form\SpeakersContactType;
use AppBundle\SpeakerInfos\Form\SpeakersDinerType;
use AppBundle\SpeakerInfos\Form\SpeakersExpensesType;
use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SpeakerPage extends AbstractController
{
    private TalkRepository $talkRepository;
    private SpeakerRepository $speakerRepository;
    private SpeakersExpensesStorage $speakersExpensesStorage;

    public function __construct(
        TalkRepository $talkRepository,
        SpeakerRepository $speakerRepository,
        SpeakersExpensesStorage $speakersExpensesStorage
    ) {
        $this->talkRepository = $talkRepository;
        $this->speakerRepository = $speakerRepository;
        $this->speakersExpensesStorage = $speakersExpensesStorage;
    }

    public function handleRequest(Request $request, Event $event, Speaker $speaker)
    {
        $talks = array_filter(
            iterator_to_array($this->talkRepository->getTalksBySpeaker($event, $speaker)),
            static fn (Talk $talk): bool => $talk->getScheduled()
        );

        $now = new DateTime('now');

        $speakersContactDefaults = [
            'phone_number' => $speaker->getPhoneNumber()
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

        $hotelReservationDefaults = [
            'nights' => $nights,
        ];

        $hotelReservationType = $this->createForm(HotelReservationType::class, $hotelReservationDefaults, ['event' => $event]);
        $hotelReservationType->handleRequest($request);

        $shouldDisplayHotelReservationForm = $event->getAccomodationEnabled() && $event->getDateEndHotelInfosCollection() > $now;

        if ($shouldDisplayHotelReservationForm && $hotelReservationType->isSubmitted() && $hotelReservationType->isValid()) {
            $hotelReservationData = $hotelReservationType->getData();
            $speaker->setHotelNightsArray($hotelReservationData['nights']);

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
            'speakers_diner_form' => $speakersDinerType->createView(),
            'hotel_reservation_form' => $hotelReservationType->createView(),
            'speakers_contact_form' => $speakersContactType->createView(),
            'day_before_event' => DateTimeImmutable::createFromMutable($event->getDateStart())->modify('- 1 day'),
        ]);
    }

    /**
     * @param Talk[] $talks
     *
     * @return array<array{talk: Talk, speaker: Speaker, room: mixed, planning: mixed, ".aggregation": array<string, mixed>}>
     */
    protected function addTalkInfos(Event $event, array $talks): array
    {
        $allTalks = $this->talkRepository->getByEventWithSpeakers($event, false);
        $allTalksById = [];
        foreach ($allTalks as $allTalk) {
            $allTalksById[$allTalk['talk']->getId()] = $allTalk;
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
