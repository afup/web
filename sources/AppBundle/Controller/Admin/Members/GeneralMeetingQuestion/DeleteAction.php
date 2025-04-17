<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\Association\Model\Repository\GeneralMeetingVoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DeleteAction extends AbstractController
{
    private GeneralMeetingQuestionRepository $generalMeetingQuestionRepository;
    private GeneralMeetingVoteRepository $generalMeetingVoteRepository;

    public function __construct(
        GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        GeneralMeetingVoteRepository $generalMeetingVoteRepository
    ) {
        $this->generalMeetingQuestionRepository = $generalMeetingQuestionRepository;
        $this->generalMeetingVoteRepository = $generalMeetingVoteRepository;
    }

    public function __invoke(Request $request, $id): RedirectResponse
    {
        /** @var GeneralMeetingQuestion $question */
        $question = $this->generalMeetingQuestionRepository->get($id);

        if (null === $question) {
            throw $this->createNotFoundException(sprintf('Question %d not found', $id));
        }

        $results = $this->generalMeetingVoteRepository->getResultsForQuestionId($question->getId());
        if (true === $question->hasVotes($results)) {
            throw $this->createAccessDeniedException('Seules les questions sans vote peuvent être supprimées');
        }

        $this->generalMeetingQuestionRepository->delete($question);
        $this->addFlash('notice', 'La question a été supprimée');

        return $this->redirectToRoute('admin_members_general_vote_list', [
            'date' => $question->getDate()->format('U')
        ]);
    }
}
