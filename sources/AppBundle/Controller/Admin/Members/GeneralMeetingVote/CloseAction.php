<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingVote;

use AppBundle\AssembleeGenerale\Entity\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CloseAction extends AbstractController
{
    public function __construct(private readonly QuestionRepository $questionRepository) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $questionId = $request->query->getInt('id');

        $question = $this->questionRepository->find($questionId);

        if (null === $question) {
            throw $this->createNotFoundException(sprintf("Question %d not found", $questionId));
        }

        if (false === $question->hasStatusOpened()) {
            throw $this->createAccessDeniedException("Only questions with status opened can be opened");
        }

        $this->questionRepository->close($question);

        $this->addFlash('notice', 'Le vote a été fermée');

        return $this->redirectToRoute('admin_members_general_vote_list', [
            'date' => $question->date->format('U'),
        ]);
    }
}
