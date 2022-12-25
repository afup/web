<?php

namespace AppBundle\Controller\Admin\Members\GeneralMeetingVote;

use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\Association\Model\Repository\GeneralMeetingVoteRepository;
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
     * @var GeneralMeetingVoteRepository
     */
    private $generalMeetingVoteRepository;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        GeneralMeetingRepository $generalMeetingRepository,
        GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        GeneralMeetingVoteRepository $generalMeetingVoteRepository,
        Environment $twig
    ) {
        $this->generalMeetingRepository = $generalMeetingRepository;
        $this->twig = $twig;
        $this->generalMeetingQuestionRepository = $generalMeetingQuestionRepository;
        $this->generalMeetingVoteRepository = $generalMeetingVoteRepository;
    }

    public function __invoke(Request $request)
    {
        $dates = $this->generalMeetingRepository->getAllDates();
        $latestDate = $this->generalMeetingRepository->getLatestDate();

        $selectedDate = $latestDate;
        if ($request->query->has('date')) {
            $selectedDate = DateTimeImmutable::createFromFormat('U', $request->get('date')) ?: null;
        }

        $rows = [];
        foreach ($this->generalMeetingQuestionRepository->loadByDate($selectedDate) as $question) {
            $rows[] = [
                'question' => $question,
                'results' => $this->generalMeetingVoteRepository->getResultsForQuestionId($question->getId()),
            ];
        }

        return new Response($this->twig->render('admin/members/general_meeting_vote/list.html.twig', [
            'dates' => $dates,
            'latestDate' => $latestDate,
            'selectedDate' => $selectedDate,
            'rows' => $rows,
        ]));
    }
}
