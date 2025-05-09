<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\GeneralMeeting\GeneralMeetingQuestionFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class EditAction extends AbstractController
{
    public function __construct(private readonly GeneralMeetingQuestionRepository $generalMeetingQuestionRepository)
    {
    }

    public function __invoke(Request $request, $id)
    {
        /** @var GeneralMeetingQuestion $question */
        $question = $this->generalMeetingQuestionRepository->get($id);

        if (null === $question) {
            throw $this->createNotFoundException(sprintf('Question %d not found', $id));
        }

        if (true !== $question->hasStatusWaiting()) {
            throw $this->createAccessDeniedException('Seules les questions en attente peuvent être modifiées');
        }

        $form = $this->createForm(GeneralMeetingQuestionFormType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->generalMeetingQuestionRepository->save($question);
            $this->addFlash('notice', 'La question a été modifiée');

            return $this->redirectToRoute('admin_members_general_vote_list', [
                'date' => $question->getDate()->format('U'),
            ]);
        }

        return $this->render('admin/members/general_meeting_question/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
