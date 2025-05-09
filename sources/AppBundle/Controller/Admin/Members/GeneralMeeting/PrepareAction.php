<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\GeneralMeeting\PrepareFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PrepareAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(private GeneralMeetingRepository $generalMeetingRepository)
    {
    }

    public function __invoke(Request $request)
    {
        $form = $this->createForm(PrepareFormType::class, ['date' => new DateTime()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($this->generalMeetingRepository->prepare($data['date'], $data['description'])) {
                $this->log('Ajout de la préparation des personnes physiques à l\'assemblée générale');
                $this->addFlash('notice', 'La préparation des personnes physiques a été ajoutée');

                return new RedirectResponse($request->getRequestUri());
            }
            $this->addFlash('error', 'Une erreur est survenue lors de la préparation des personnes physiques');
        }

        return $this->render('admin/members/general_meeting/prepare.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
