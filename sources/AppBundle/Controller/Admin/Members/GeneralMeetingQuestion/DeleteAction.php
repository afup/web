<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\Association\Model\Repository\GeneralMeetingVoteRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DeleteAction
{
    private GeneralMeetingQuestionRepository $generalMeetingQuestionRepository;
    private GeneralMeetingVoteRepository $generalMeetingVoteRepository;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        GeneralMeetingVoteRepository $generalMeetingVoteRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->generalMeetingQuestionRepository = $generalMeetingQuestionRepository;
        $this->generalMeetingVoteRepository = $generalMeetingVoteRepository;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request, $id): RedirectResponse
    {
        /** @var GeneralMeetingQuestion $question */
        $question = $this->generalMeetingQuestionRepository->get($id);

        if (null === $question) {
            throw new NotFoundHttpException(sprintf('Question %d not found', $id));
        }

        $results = $this->generalMeetingVoteRepository->getResultsForQuestionId($question->getId());

        if (true === $question->hasVotes($results)) {
            throw new AccessDeniedHttpException('Seules les questions sans vote peuvent être supprimées');
        }

        $this->generalMeetingQuestionRepository->delete($question);
        $this->flashBag->add('notice', 'La question a été supprimée');

        return new RedirectResponse($this->urlGenerator->generate('admin_members_general_vote_list', [
            'date' => $question->getDate()->format('U')
        ]));
    }
}
