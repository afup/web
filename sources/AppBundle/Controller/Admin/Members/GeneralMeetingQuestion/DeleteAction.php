<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\AssembleeGenerale\Entity\Repository\QuestionRepository;
use AppBundle\AssembleeGenerale\Entity\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteAction extends AbstractController
{
    public function __construct(
        private readonly QuestionRepository $questionRepository,
        private readonly VoteRepository $voteRepository,
    ) {}

    public function __invoke(int $id): RedirectResponse
    {
        $question = $this->questionRepository->find($id);

        if (null === $question) {
            throw $this->createNotFoundException(sprintf('Question %d not found', $id));
        }

        $results = $this->voteRepository->getResultsForQuestionId($question->id);
        if (true === $question->hasVotes($results)) {
            throw $this->createAccessDeniedException('Seules les questions sans vote peuvent être supprimées');
        }

        $this->questionRepository->delete($question);
        $this->addFlash('notice', 'La question a été supprimée');

        return $this->redirectToRoute('admin_members_general_vote_list', [
            'date' => $question->date->format('U'),
        ]);
    }
}
