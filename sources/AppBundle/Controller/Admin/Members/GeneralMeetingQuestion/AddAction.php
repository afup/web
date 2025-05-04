<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\GeneralMeeting\GeneralMeetingQuestionFormType;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddAction extends AbstractController
{
    public function __construct(
        private readonly GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        private readonly GeneralMeetingRepository $generalMeetingRepository,
    ) {
    }

    public function __invoke(Request $request, $date): Response
    {
        $date = \DateTimeImmutable::createFromFormat('U', $date);
        $generalMeeting = $this->generalMeetingRepository->findOneByDate($date);
        if (!$generalMeeting) {
            throw $this->createNotFoundException(sprintf('L\'assemblée générale en date du %s n\'a pas été trouvée', $date->format('d/m/Y')));
        }

        $question = new GeneralMeetingQuestion();
        $question->setDate($generalMeeting['date']);
        $question->setCreatedAt(new \DateTime());

        $form = $this->createForm(GeneralMeetingQuestionFormType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->generalMeetingQuestionRepository->save($question);
            $this->addFlash('notice', 'La question a été ajoutée');

            return $this->redirectToRoute('admin_members_general_vote_list', [
                'date' =>  $question->getDate()->format('U'),
            ]);
        }

        return $this->render('admin/members/general_meeting_question/add.html.twig', [
            'general_meeting' => $generalMeeting,
            'form' => $form->createView(),
        ]);
    }
}
