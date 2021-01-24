<?php

namespace AppBundle\Controller\Admin\Members\GeneralMeetingVote;

use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListAction
{
    /**
     * @var GeneralMeetingRepository
     */
    private $generalMeetingRepository;

    /**
     * @var GeneralMeetingQuestionRepository
     */
    private $generalMeetingQuestionRepository;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        GeneralMeetingRepository $generalMeetingRepository,
        GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        Environment $twig
    ) {
        $this->generalMeetingRepository = $generalMeetingRepository;
        $this->twig = $twig;
        $this->generalMeetingQuestionRepository = $generalMeetingQuestionRepository;
    }

    public function __invoke(Request $request)
    {
        $dates = $this->generalMeetingRepository->getAllDates();
        $latestDate = $this->generalMeetingRepository->getLatestDate();

        $selectedDate = $latestDate;
        if ($request->query->has('date')) {
            $selectedDate = DateTimeImmutable::createFromFormat('U', $request->get('date')) ?: null;
        }

        return new Response($this->twig->render('admin/members/general_meeting_vote/list.html.twig', [
            'dates' => $dates,
            'latestDate' => $latestDate,
            'selectedDate' => $selectedDate,
            'questions' => $this->generalMeetingQuestionRepository->loadByDate($selectedDate),
        ]));
    }
}
