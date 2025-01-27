<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingVote;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OpenAction
{
    private FlashBagInterface $flashBag;

    private GeneralMeetingQuestionRepository $generalMeetingQuestionRepository;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->flashBag = $flashBag;
        $this->generalMeetingQuestionRepository = $generalMeetingQuestionRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $questionId = $request->query->getInt('id');

        /** @var GeneralMeetingQuestion $question */
        $question = $this->generalMeetingQuestionRepository->get($questionId);

        if (null === $question) {
            throw new NotFoundHttpException(sprintf("Question %d not found", $questionId));
        }

        if (false === $question->hasStatusWaiting()) {
            throw new AccessDeniedHttpException("Only questions with status waiting can be opened");
        }

        $this->generalMeetingQuestionRepository->open($question);

        $this->flashBag->add('notice', 'Le vote a été ouvert');

        return new RedirectResponse($this->urlGenerator->generate('admin_members_general_vote_list', ['date' => $question->getDate()->format('U')]));
    }
}
