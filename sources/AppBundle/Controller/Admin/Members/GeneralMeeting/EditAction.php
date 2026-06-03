<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use AppBundle\AssembleeGenerale\Entity\Repository\AssembleeGeneraleRepository;
use AppBundle\AssembleeGenerale\Form\PrepareFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditAction extends AbstractController
{
    public function __construct(private readonly AssembleeGeneraleRepository $assembleGeneraleRepository) {}

    public function __invoke(Request $request): Response
    {
        $date = new DateTime('@' . $request->query->get('date'));

        $assemblee = $this->assembleGeneraleRepository->findOneByDate($date);
        if (null === $assemblee) {
            throw $this->createNotFoundException(sprintf('General meeting with date "%d" not found', $date->getTimestamp()));
        }
        $form = $this->createForm(PrepareFormType::class, ['description' => $assemblee->description], ['without_date' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $description = is_string($data['description'] ?? null) ? $data['description'] : '';
            $this->assembleGeneraleRepository->upsert($date, $description);

            $this->addFlash('success', 'Description enregistrée');
            return $this->redirectToRoute('admin_members_general_meeting_edit', [
                'date' => $assemblee->date,
            ]);
        }

        return $this->render('admin/members/general_meeting/edit.html.twig', [
            'form' => $form->createView(),
            'generalMeetingDate' => $date,
        ]);
    }
}
