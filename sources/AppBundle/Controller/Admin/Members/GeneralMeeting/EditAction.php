<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\GeneralMeeting\PrepareFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(private GeneralMeetingRepository $generalMeetingRepository)
    {
    }

    public function __invoke(Request $request): Response
    {
        $date = new DateTime('@' . $request->query->get('date'));

        $generaleMeeting = $this->generalMeetingRepository->findOneByDate($date);
        if (null === $generaleMeeting) {
            throw $this->createNotFoundException(sprintf('General meeting with date "%d" not found', $date->getTimestamp()));
        }
        $form = $this->createForm(PrepareFormType::class, $generaleMeeting, ['without_date' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->generalMeetingRepository->save($generaleMeeting['date'], $data['description']);

            $this->addFlash('success', 'Description enregistrée');
            return $this->redirectToRoute('admin_members_general_meeting_edit', [
                'date' => $date->getTimestamp(),
            ]);
        }

        return $this->render('admin/members/general_meeting/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
