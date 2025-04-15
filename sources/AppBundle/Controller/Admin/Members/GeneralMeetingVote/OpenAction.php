<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingVote;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class OpenAction extends AbstractController
{
    private GeneralMeetingQuestionRepository $generalMeetingQuestionRepository;

    public function __construct(GeneralMeetingQuestionRepository $generalMeetingQuestionRepository)
    {
        $this->generalMeetingQuestionRepository = $generalMeetingQuestionRepository;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $questionId = $request->query->getInt('id');

        /** @var GeneralMeetingQuestion $question */
        $question = $this->generalMeetingQuestionRepository->get($questionId);

        if (null === $question) {
            throw $this->createNotFoundException(sprintf("Question %d not found", $questionId));
        }

        if (false === $question->hasStatusWaiting()) {
            throw $this->createAccessDeniedException("Only questions with status waiting can be opened");
        }

        $this->generalMeetingQuestionRepository->open($question);

        $this->addFlash('notice', 'Le vote a été ouvert');

        return $this->redirectToRoute('admin_members_general_vote_list', [
            'date' => $question->getDate()->format('U')
        ]);
    }
}
