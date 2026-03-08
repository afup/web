<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Webmozart\Assert\Assert;

class ListAction
{
    public const VALID_SORTS = ['nom', 'date_consultation', 'presence', 'personnes_avec_pouvoir_nom'];
    public const VALID_DIRECTIONS = ['asc', 'desc'];

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $latestDate = $this->generalMeetingRepository->getLatestAttendanceDate();
        $sort = $request->query->get('sort', 'nom');
        $direction = $request->query->get('direction', 'asc');
        Assert::inArray($sort, self::VALID_SORTS);
        Assert::inArray($direction, self::VALID_DIRECTIONS);
        $dates = $this->generalMeetingRepository->getAllDates();
        $convocations = count($this->userRepository->getActiveMembers());
        $nbAttendeesAndPowers = $nbAttendees = $quorum = $validAttendeeIds = null;
        if (null !== $latestDate) {
            $nbAttendeesAndPowers = $this->generalMeetingRepository->countAttendeesAndPowers($latestDate);
            $nbAttendees = $this->generalMeetingRepository->countAttendees($latestDate);
            $quorum = $this->generalMeetingRepository->obtenirEcartQuorum($latestDate, $convocations);
            $validAttendeeIds = $this->generalMeetingRepository->getValidAttendeeIds($latestDate);
        }
        $selectedDate = $latestDate;
        if ($request->query->has('date')) {
            $selectedDate = \DateTimeImmutable::createFromFormat('U', $request->get('date')) ?: null;
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
