<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\AssembleeGenerale\Entity\Repository\QuestionRepository;
use AppBundle\AssembleeGenerale\Form\GeneralMeetingQuestionFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditAction extends AbstractController
{
    public function __construct(private readonly QuestionRepository $questionRepository) {}

    public function __invoke(Request $request, int $id): Response
    {
        $question = $this->questionRepository->find($id);

        if (null === $question) {
            throw $this->createNotFoundException(sprintf('Question %d not found', $id));
        }

        if (true !== $question->hasStatusWaiting()) {
            throw $this->createAccessDeniedException('Seules les questions en attente peuvent être modifiées');
        }

        $form = $this->createForm(GeneralMeetingQuestionFormType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionRepository->save($question);
            $this->addFlash('notice', 'La question a été modifiée');

            return $this->redirectToRoute('admin_members_general_vote_list', [
                'date' => $question->date->format('U'),
            ]);
        }

        return $this->render('admin/members/general_meeting_question/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
