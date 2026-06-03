<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingVote;

use AppBundle\AssembleeGenerale\Entity\Repository\QuestionRepository;
use AppBundle\AssembleeGenerale\Entity\Repository\VoteRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListAction
{
    public function __construct(
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly VoteRepository $voteRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $dates = $this->generalMeetingRepository->getAllDates();
        $latestDate = $this->generalMeetingRepository->getLatestAttendanceDate();

        $selectedDate = $latestDate;
        if ($request->query->has('date')) {
            $selectedDate = DateTimeImmutable::createFromFormat('U', $request->get('date')) ?: null;
        }

        $rows = [];
        foreach ($this->questionRepository->loadByDate($selectedDate) as $question) {
            $rows[] = [
                'question' => $question,
                'results' => $this->voteRepository->getResultsForQuestionId($question->id),
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
