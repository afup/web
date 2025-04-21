<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListAction
{
    const VALID_SORTS = ['nom', 'date_consultation', 'presence', 'personnes_avec_pouvoir_nom'];
    const VALID_DIRECTIONS = ['asc', 'desc'];
    private UserRepository $userRepository;
    private GeneralMeetingRepository $generalMeetingRepository;
    private Environment $twig;

    public function __construct(
        UserRepository $userRepository,
        GeneralMeetingRepository $generalMeetingRepository,
        Environment $twig
    ) {
        $this->userRepository = $userRepository;
        $this->generalMeetingRepository = $generalMeetingRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $latestDate = $this->generalMeetingRepository->getLatestDate();
        $sort = $request->query->get('sort', 'nom');
        $direction = $request->query->get('direction', 'asc');
        Assertion::inArray($sort, self::VALID_SORTS);
        Assertion::inArray($direction, self::VALID_DIRECTIONS);
        $dates = $this->generalMeetingRepository->getAllDates();
        $convocations = count($this->userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL));
        $nbAttendeesAndPowers = $nbAttendees = $quorum = $validAttendeeIds = null;
        if (null !== $latestDate) {
            $nbAttendeesAndPowers = $this->generalMeetingRepository->countAttendeesAndPowers($latestDate);
            $nbAttendees = $this->generalMeetingRepository->countAttendees($latestDate);
            $quorum = $this->generalMeetingRepository->obtenirEcartQuorum($latestDate, $convocations);
            $validAttendeeIds = $this->generalMeetingRepository->getValidAttendeeIds($latestDate);
        }
        $selectedDate = $latestDate;
        if ($request->query->has('date')) {
            $selectedDate = new \DateTimeImmutable('@' . $request->get('date')) ?: null;
        }
        $attendees = null !== $selectedDate ? $this->generalMeetingRepository->getAttendees($selectedDate, $sort, $direction) : [];

        return new Response($this->twig->render('admin/members/general_meeting/list.html.twig', [
            'convocations' => $convocations,
            'nbAttendeesAndPowers' => $nbAttendeesAndPowers,
            'nbAttendees' => $nbAttendees,
            'quorum' => $quorum,
            'attendees' => $attendees,
            'validAttendeeIds' => $validAttendeeIds,
            'dates' => $dates,
            'selectedDate' => $selectedDate,
            'latestDate' => $latestDate,
            'sort' => $sort,
            'direction' => $direction,
        ]));
    }
}
