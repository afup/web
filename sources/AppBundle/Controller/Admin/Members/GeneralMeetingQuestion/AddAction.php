<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\AssembleeGenerale\Entity\Question;
use AppBundle\AssembleeGenerale\Entity\Repository\AssembleeGeneraleRepository;
use AppBundle\AssembleeGenerale\Entity\Repository\QuestionRepository;
use AppBundle\AssembleeGenerale\Form\GeneralMeetingQuestionFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddAction extends AbstractController
{
    public function __construct(
        private readonly QuestionRepository $questionRepository,
        private readonly AssembleeGeneraleRepository $assembleGeneraleRepository,
    ) {}

    public function __invoke(Request $request, $date): Response
    {
        $date = \DateTimeImmutable::createFromFormat('U', $date);
        $generalMeeting = $this->assembleGeneraleRepository->findOneByDate($date);
        if (!$generalMeeting) {
            throw $this->createNotFoundException(sprintf('L\'assemblée générale en date du %s n\'a pas été trouvée', $date->format('d/m/Y')));
        }

        $question = new Question();
        $question->date = $generalMeeting->date;
        $question->dateCreation = new \DateTime();

        $form = $this->createForm(GeneralMeetingQuestionFormType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionRepository->save($question);
            $this->addFlash('notice', 'La question a été ajoutée');

            return $this->redirectToRoute('admin_members_general_vote_list', [
                'date' =>  $question->date->format('U'),
            ]);
        }

        return $this->render('admin/members/general_meeting_question/add.html.twig', [
            'general_meeting' => $generalMeeting,
            'form' => $form->createView(),
        ]);
    }
}
