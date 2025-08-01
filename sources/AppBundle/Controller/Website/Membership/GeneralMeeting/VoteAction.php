<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\GeneralMeeting;

use Afup\Site\Droits;
use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\GeneralMeetingVote;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\Association\Model\Repository\GeneralMeetingVoteRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class VoteAction extends AbstractController
{
    public function __construct(
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        private readonly GeneralMeetingVoteRepository $generalMeetingVoteRepository,
        private readonly Droits $droits,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        if (null === ($questionId = $request->get('questionId'))) {
            throw $this->createNotFoundException('QuestionId manquant');
        }

        if (false === GeneralMeetingVote::isValueAllowed($vote = $request->query->getAlpha('vote'))) {
            throw $this->createNotFoundException('Vote manquant');
        }

        /** @var GeneralMeetingQuestion $question */
        $question = $this->generalMeetingQuestionRepository->get($questionId);

        if (null === $question) {
            throw $this->createNotFoundException('QuestionId missing');
        }

        $redirection = $this->redirectToRoute('member_general_meeting');

        if (false === $question->hasStatusOpened()) {
            $this->addFlash('error', "Ce vote n'est pas ouvert");
            return $redirection;
        }

        $userId = $this->droits->obtenirIdentifiant();

        if (null !== $this->generalMeetingVoteRepository->loadByQuestionIdAndUserId($questionId, $userId)) {
            $this->addFlash('error', 'Vous avez déjà voté pour cette question');
            return $redirection;
        }

        $weight = 1 + count($this->generalMeetingRepository->getAttendees($question->getDate(), 'nom', 'asc', $userId));

        $generalMeetingVote = new GeneralMeetingVote();
        $generalMeetingVote
            ->setQuestionId($question->getId())
            ->setUserId($this->droits->obtenirIdentifiant())
            ->setWeight($weight)
            ->setValue($vote)
            ->setCreatedAt(new \DateTime())
        ;

        $this->generalMeetingVoteRepository->save($generalMeetingVote);

        $this->addFlash('notice', 'Votre vote a été pris en compte');

        return $redirection;
    }
}
