<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingVote;

use AppBundle\AssembleeGenerale\Entity\Repository\QuestionRepository;
use AppBundle\AssembleeGenerale\Entity\Repository\VoteRepository;
use AppBundle\AssembleeGenerale\Entity\Repository\PresenceRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListAction
{
    public function __construct(
        private readonly PresenceRepository $presenceRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly VoteRepository $voteRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request): Response
    {
        $dates = $this->presenceRepository->getAllDates();
        $latestDate = $this->presenceRepository->getLatestAttendanceDate();

        $selectedDate = $latestDate;
        if ($request->query->has('date')) {
            $selectedDate = DateTimeImmutable::createFromFormat('U', $request->get('date')) ?: null;
        }

        $rows = [];
        if (null !== $selectedDate) {
            foreach ($this->questionRepository->loadByDate($selectedDate) as $question) {
                $rows[] = [
                    'question' => $question,
                    'results' => $this->voteRepository->getResultsForQuestionId($question->id),
                ];
            }
        }

        return new Response($this->twig->render('admin/members/general_meeting_vote/list.html.twig', [
            'dates' => $dates,
            'latestDate' => $latestDate,
            'selectedDate' => $selectedDate,
            'rows' => $rows,
        ]));
    }
}
